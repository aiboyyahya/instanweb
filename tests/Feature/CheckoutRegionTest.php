<?php

namespace Tests\Feature;

use App\Http\Controllers\HomeController;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class CheckoutRegionTest extends TestCase
{
    use RefreshDatabase;

    public function test_region_options_are_filtered_by_parent_and_search(): void
    {
        $this->getJson(route('regions.provinces'))
            ->assertOk()
            ->assertJsonFragment(['id' => '32', 'name' => 'Jawa Barat']);

        $this->getJson(route('regions.provinces', ['search' => 'jawa barat']))
            ->assertOk()
            ->assertJsonCount(1)
            ->assertJsonFragment(['id' => '32', 'name' => 'Jawa Barat']);

        $this->getJson(route('regions.regencies', ['province_id' => '32']))
            ->assertOk()
            ->assertJsonFragment(['id' => '3273', 'name' => 'Kota Bandung']);

        $this->getJson(route('regions.districts', ['city_id' => '3273']))
            ->assertOk()
            ->assertJsonFragment(['id' => '327301', 'name' => 'Sukasari']);

        $this->getJson(route('regions.regencies'))
            ->assertOk()
            ->assertExactJson([]);
    }

    public function test_checkout_page_renders_region_fields(): void
    {
        [$user, $product] = $this->cartContext();

        $this->actingAs($user)
            ->withSession([
                'cart' => [
                    $product->id => [
                        'name' => $product->product_name,
                        'price' => $product->purchase_price,
                        'quantity' => 1,
                        'image' => $product->image,
                        'weight' => 1000,
                    ],
                ],
            ])
            ->get(route('checkout.page'))
            ->assertOk()
            ->assertSee('Provinsi')
            ->assertSee('Kota/Kabupaten')
            ->assertSee('Kecamatan')
            ->assertSee(route('regions.provinces'), false);
    }

    public function test_checkout_stores_region_names_from_selected_ids(): void
    {
        [$user, $product] = $this->cartContext();

        $this->actingAs($user)
            ->withSession($this->directCheckout($product))
            ->post(route('checkout'), [
                'province_id' => '32',
                'city_id' => '3273',
                'district_id' => '327301',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 10',
                'postal_code' => '40152',
                'courier' => 'JNE',
                'shipping_cost' => 0,
                'notes' => 'Titip di pos keamanan.',
            ])
            ->assertRedirect();

        $transaction = Transaction::firstOrFail();

        $this->assertSame('Jawa Barat', $transaction->province);
        $this->assertSame('Kota Bandung', $transaction->city);
        $this->assertSame('Sukasari', $transaction->district);
        $this->assertSame('40152', $transaction->postal_code);

        $this->assertSame(4, $product->fresh()->stock);
    }

    public function test_checkout_rejects_region_that_does_not_belong_to_its_parent(): void
    {
        [$user, $product] = $this->cartContext();

        $this->actingAs($user)
            ->withSession($this->directCheckout($product))
            ->post(route('checkout'), [
                'province_id' => '32',
                'city_id' => '3171',
                'district_id' => '327301',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 10',
                'postal_code' => '40152',
                'courier' => 'JNE',
                'shipping_cost' => 0,
            ])
            ->assertSessionHasErrors('province_id');

        $this->assertDatabaseCount('transactions', 0);
        $this->assertSame(5, $product->fresh()->stock);
    }

    public function test_checkout_uses_current_product_price_from_database(): void
    {
        [$user, $product] = $this->cartContext();

        // Simulasikan harga lama/tidak sinkron di session.
        $session = $this->directCheckout($product);
        $session['direct_checkout'][$product->id]['price'] = 1;

        $this->actingAs($user)
            ->withSession($session)
            ->post(route('checkout'), [
                'province_id' => '32',
                'city_id' => '3273',
                'district_id' => '327301',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 10',
                'postal_code' => '40152',
                'courier' => 'JNE',
                'shipping_cost' => 0,
            ])
            ->assertRedirect();

        $transaction = Transaction::with('items')->firstOrFail();

        $this->assertEquals(10000, (float) $transaction->total);
        $this->assertEquals(10000, (float) $transaction->items->first()->price);
    }

    public function test_checkout_pages_are_scoped_to_the_authenticated_customer(): void
    {
        [$owner] = $this->cartContext();

        $transaction = Transaction::create([
            'order_code' => 'INV-SCOPE-1',
            'customer_id' => $owner->id,
            'address' => 'Alamat pemilik transaksi',
            'status' => 'pending',
            'total' => 10000,
            'payment_method' => 'manual',
            'payment_status' => 'pending',
        ]);

        $otherUser = User::factory()->create([
            'role' => 'user',
            'phone_number' => null,
        ]);

        $this->actingAs($otherUser)
            ->get(route('checkout.payment', $transaction->id))
            ->assertNotFound();

        $this->actingAs($otherUser)
            ->get(route('checkout.success', $transaction->id))
            ->assertNotFound();

        $this->actingAs($owner)
            ->get(route('checkout.payment', $transaction->id))
            ->assertOk();

        $this->actingAs($owner)
            ->get(route('checkout.success', $transaction->id))
            ->assertOk();
    }

    public function test_order_confirmation_uses_the_store_that_owns_the_products(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        Store::create([
            'user_id' => $ownerA->id,
            'store_name' => 'Toko Pertama',
        ]);

        $storeB = Store::create([
            'user_id' => $ownerB->id,
            'store_name' => 'Toko Kedua',
        ]);

        $product = Product::create([
            'store_id' => $storeB->id,
            'product_name' => 'Produk Toko Kedua',
            'slug' => 'produk-toko-kedua',
            'description' => 'Produk uji notifikasi.',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'status' => 'active',
            'image' => 'products/uji.jpg',
        ]);

        $customer = User::factory()->create([
            'role' => 'user',
            'phone_number' => '08123456789',
        ]);

        $transaction = Transaction::create([
            'order_code' => 'INV-NOTIF-1',
            'customer_id' => $customer->id,
            'phone_number' => '08123456789',
            'address' => 'Jl. Uji No. 1',
            'status' => 'pending',
            'total' => 10000,
            'payment_method' => 'manual',
            'payment_status' => 'pending',
        ]);

        TransactionItem::create([
            'transaction_id' => $transaction->id,
            'product_id' => $product->id,
            'price' => 10000,
            'quantity' => 1,
        ]);

        $controller = Mockery::mock(HomeController::class)->makePartial();
        $controller->shouldReceive('sendMessage')
            ->once()
            ->withArgs(function (string $phone, string $message): bool {
                return str_contains($message, 'Toko Kedua')
                    && ! str_contains($message, 'Toko Pertama');
            })
            ->andReturnTrue();

        $this->assertTrue($controller->sendOrderConfirmation($transaction->fresh()));
    }

    /**
     * @return array{0: User, 1: Product}
     */
    private function cartContext(): array
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Uji Checkout',
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'product_name' => 'Produk Uji',
            'slug' => 'produk-uji-checkout',
            'description' => 'Produk untuk pengujian checkout.',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'status' => 'active',
            'image' => 'products/uji.jpg',
        ]);

        $user = User::factory()->create([
            'role' => 'user',
            'phone_number' => null,
        ]);

        return [$user, $product];
    }

    /**
     * @return array<string, mixed>
     */
    private function directCheckout(Product $product): array
    {
        return [
            'direct_checkout' => [
                $product->id => [
                    'name' => $product->product_name,
                    'price' => $product->purchase_price,
                    'quantity' => 1,
                    'image' => $product->image,
                    'weight' => 1000,
                ],
            ],
        ];
    }
}
