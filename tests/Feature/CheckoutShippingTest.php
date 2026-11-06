<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use App\Services\ShippingCostService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutShippingTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_calculates_shipping_from_store_and_destination_coordinates(): void
    {
        [$user, $product, $store] = $this->cartContext(-6.2, 106.816667);

        $destinationLat = -6.2;
        $destinationLng = 106.9;

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
                'latitude' => $destinationLat,
                'longitude' => $destinationLng,
                'payment_method' => 'cod',
            ])
            ->assertRedirect();

        $transaction = Transaction::firstOrFail();
        $estimate = app(ShippingCostService::class)->estimate(
            (float) $store->latitude,
            (float) $store->longitude,
            $destinationLat,
            $destinationLng,
            'JNE',
        );

        $this->assertSame('cod', $transaction->payment_method);
        $this->assertEqualsWithDelta($estimate['distance_km'], $transaction->distance_km, 0.01);
        $this->assertSame($estimate['shipping_cost'], (int) $transaction->shipping_cost);
        $this->assertGreaterThan(0, $transaction->shipping_cost);
        $this->assertEqualsWithDelta(
            $product->purchase_price + $estimate['shipping_cost'],
            (float) $transaction->total,
            0.01,
        );
        $this->assertEqualsWithDelta($destinationLat, (float) $transaction->latitude, 0.0000001);
        $this->assertEqualsWithDelta($destinationLng, (float) $transaction->longitude, 0.0000001);
    }

    public function test_shipping_estimate_endpoint_returns_distance_and_cost(): void
    {
        [$user, $product, $store] = $this->cartContext(-6.2, 106.816667);

        $response = $this->actingAs($user)
            ->withSession(['active_store_id' => $store->id])
            ->postJson(route('checkout.shipping-estimate'), [
                'latitude' => -6.2,
                'longitude' => 106.9,
                'courier' => 'JNE',
            ]);

        $response->assertOk()->assertJson([
            'origin_configured' => true,
            'courier' => 'JNE',
            'courier_label' => 'JNE',
        ]);

        $estimate = app(ShippingCostService::class)->estimate(
            (float) $store->latitude,
            (float) $store->longitude,
            -6.2,
            106.9,
            'JNE',
        );

        $this->assertEqualsWithDelta($estimate['distance_km'], $response->json('distance_km'), 0.01);
        $this->assertSame($estimate['shipping_cost'], $response->json('shipping_cost'));

        $jntResponse = $this->actingAs($user)
            ->withSession(['active_store_id' => $store->id])
            ->postJson(route('checkout.shipping-estimate'), [
                'latitude' => -6.2,
                'longitude' => 106.9,
                'courier' => 'J&T',
            ]);

        $jntResponse->assertOk()->assertJson([
            'courier' => 'J&T',
            'courier_label' => 'J&T Express',
        ]);

        $jntEstimate = app(ShippingCostService::class)->estimate(
            (float) $store->latitude,
            (float) $store->longitude,
            -6.2,
            106.9,
            'J&T',
        );

        $this->assertSame($jntEstimate['shipping_cost'], $jntResponse->json('shipping_cost'));
        $this->assertNotSame($estimate['shipping_cost'], $jntEstimate['shipping_cost']);
    }

    public function test_shipping_cost_differs_per_courier_and_checkout_uses_selected_courier(): void
    {
        [$user, $product, $store] = $this->cartContext(-6.2, 106.816667);

        $destinationLat = -6.2;
        $destinationLng = 106.9;
        $shipping = app(ShippingCostService::class);

        $jne = $shipping->estimate(
            (float) $store->latitude,
            (float) $store->longitude,
            $destinationLat,
            $destinationLng,
            'JNE',
        );

        $jnt = $shipping->estimate(
            (float) $store->latitude,
            (float) $store->longitude,
            $destinationLat,
            $destinationLng,
            'J&T',
        );

        $this->assertSame($jne['distance_km'], $jnt['distance_km']);
        $this->assertNotSame($jne['shipping_cost'], $jnt['shipping_cost']);

        $this->actingAs($user)
            ->withSession($this->directCheckout($product))
            ->post(route('checkout'), [
                'province_id' => '32',
                'city_id' => '3273',
                'district_id' => '327301',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 10',
                'postal_code' => '40152',
                'courier' => 'J&T',
                'latitude' => $destinationLat,
                'longitude' => $destinationLng,
                'payment_method' => 'transfer_bank',
            ])
            ->assertRedirect();

        $transaction = Transaction::firstOrFail();

        $this->assertSame('J&T', $transaction->courier);
        $this->assertSame('Reguler', $transaction->courier_service);
        $this->assertSame($jnt['shipping_cost'], (int) $transaction->shipping_cost);
        $this->assertNotSame($jne['shipping_cost'], (int) $transaction->shipping_cost);
        $this->assertEqualsWithDelta($jnt['distance_km'], $transaction->distance_km, 0.01);
    }

    public function test_shipping_estimate_requires_a_supported_courier(): void
    {
        [$user, $product, $store] = $this->cartContext(-6.2, 106.816667);

        $this->actingAs($user)
            ->withSession(['active_store_id' => $store->id])
            ->postJson(route('checkout.shipping-estimate'), [
                'latitude' => -6.2,
                'longitude' => 106.9,
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('courier');

        $this->actingAs($user)
            ->withSession(['active_store_id' => $store->id])
            ->postJson(route('checkout.shipping-estimate'), [
                'latitude' => -6.2,
                'longitude' => 106.9,
                'courier' => 'Kurir Tidak Dikenal',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('courier');
    }

    public function test_checkout_rejects_unsupported_courier(): void
    {
        [$user, $product] = $this->cartContext(-6.2, 106.816667);

        $this->actingAs($user)
            ->withSession($this->directCheckout($product))
            ->post(route('checkout'), [
                'province_id' => '32',
                'city_id' => '3273',
                'district_id' => '327301',
                'phone_number' => '08123456789',
                'address' => 'Jl. Merdeka No. 10',
                'postal_code' => '40152',
                'courier' => 'Kurir Tidak Dikenal',
                'latitude' => -6.2,
                'longitude' => 106.9,
                'payment_method' => 'transfer_bank',
            ])
            ->assertSessionHasErrors('courier');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_checkout_keeps_shipping_zero_when_store_has_no_coordinates(): void
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
                'latitude' => -6.914744,
                'longitude' => 107.609810,
                'payment_method' => 'transfer_bank',
            ])
            ->assertRedirect();

        $transaction = Transaction::firstOrFail();

        $this->assertSame(0.0, $transaction->shipping_cost);
        $this->assertNull($transaction->distance_km);
        $this->assertEqualsWithDelta($product->purchase_price, (float) $transaction->total, 0.01);
    }

    public function test_checkout_requires_destination_coordinates(): void
    {
        [$user, $product] = $this->cartContext(-6.2, 106.816667);

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
                'payment_method' => 'transfer_bank',
            ])
            ->assertSessionHasErrors('latitude');

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_owner_can_save_store_coordinates_from_profile(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);

        $store = Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Titik Lokasi',
        ]);

        $this->actingAs($owner)
            ->put(route('profil.store.update'), [
                'store_name' => 'Toko Titik Lokasi',
                'latitude' => -6.2,
                'longitude' => 106.816667,
            ])
            ->assertRedirect();

        $store->refresh();

        $this->assertEqualsWithDelta(-6.2, (float) $store->latitude, 0.0000001);
        $this->assertEqualsWithDelta(106.816667, (float) $store->longitude, 0.0000001);
    }

    /**
     * @return array{0: User, 1: Product, 2: Store}
     */
    private function cartContext(?float $latitude = null, ?float $longitude = null): array
    {
        $owner = User::factory()->create(['role' => 'admin']);

        $store = Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Uji Ongkir',
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        $product = Product::create([
            'store_id' => $store->id,
            'product_name' => 'Produk Uji Ongkir',
            'slug' => 'produk-uji-ongkir',
            'description' => 'Produk untuk pengujian ongkir.',
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

        return [$user, $product, $store];
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
