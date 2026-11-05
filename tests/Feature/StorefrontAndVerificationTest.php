<?php

namespace Tests\Feature;

use App\Mail\VerificationCodeMail;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StorefrontAndVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_beranda_lists_stores_and_links_to_each_storefront(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Kopi Nusantara',
            'description' => 'Kopi pilihan dari Gayo.',
        ]);

        $this->get('/beranda')
            ->assertOk()
            ->assertSee('Toko Kopi Nusantara')
            ->assertSee(route('store.show', $store), false);
    }

    public function test_storefront_shows_only_products_from_that_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = Store::create(['user_id' => $ownerA->id, 'store_name' => 'Toko A']);
        $storeB = Store::create(['user_id' => $ownerB->id, 'store_name' => 'Toko B']);

        $category = Category::create(['category_name' => 'Minuman']);

        Product::create([
            'store_id' => $storeA->id,
            'product_name' => 'Kopi A',
            'slug' => 'kopi-a',
            'description' => 'Produk toko A',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'status' => 'active',
            'category_id' => $category->id,
            'image' => 'products/a.jpg',
        ]);

        Product::create([
            'store_id' => $storeB->id,
            'product_name' => 'Teh B',
            'slug' => 'teh-b',
            'description' => 'Produk toko B',
            'purchase_price' => 8000,
            'selling_price' => 12000,
            'stock' => 5,
            'status' => 'active',
            'category_id' => $category->id,
            'image' => 'products/b.jpg',
        ]);

        $this->get(route('store.show', $storeA))
            ->assertOk()
            ->assertSee('Toko A')
            ->assertSee('Kopi A')
            ->assertDontSee('Teh B');

        $this->get(route('store.products', $storeA))
            ->assertOk()
            ->assertSee('Kopi A')
            ->assertDontSee('Teh B');
    }

    public function test_store_owner_can_upload_three_banners_from_profile(): void
    {
        Storage::fake('public');

        $owner = User::factory()->create(['role' => 'admin']);
        $store = Store::create(['user_id' => $owner->id, 'store_name' => 'Toko Banner']);

        $this->actingAs($owner)
            ->put(route('profil.store.update'), [
                'store_name' => 'Toko Banner',
                'banner_1' => UploadedFile::fake()->image('banner1.jpg', 1200, 600),
                'banner_2' => UploadedFile::fake()->image('banner2.jpg', 1200, 600),
                'banner_3' => UploadedFile::fake()->image('banner3.jpg', 1200, 600),
            ])
            ->assertRedirect(route('profil'));

        $store->refresh();

        $this->assertNotNull($store->banner_1);
        $this->assertNotNull($store->banner_2);
        $this->assertNotNull($store->banner_3);
        $this->assertCount(3, $store->bannerPaths());
    }

    public function test_unverified_user_is_redirected_from_checkout_to_verification_page(): void
    {
        $user = User::factory()->unverified()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('checkout.page'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_verification_page_renders_for_unverified_user(): void
    {
        $user = User::factory()->unverified()->create([
            'role' => 'user',
            'phone_number' => '081234567890',
            'phone_verified_at' => null,
        ]);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Verifikasi Email & Nomor Telepon')
            ->assertSee('Kirim kode ke email')
            ->assertSee('Kirim kode SMS');
    }

    public function test_email_verification_code_can_be_confirmed(): void
    {
        Mail::fake();

        $user = User::factory()->unverified()->create(['role' => 'user']);
        $code = null;

        $this->actingAs($user)
            ->post(route('verification.email.send'))
            ->assertRedirect()
            ->assertSessionHas('success');

        Mail::assertSent(VerificationCodeMail::class, function (VerificationCodeMail $mail) use (&$code) {
            $code = $mail->code;

            return true;
        });

        $this->actingAs($user)
            ->post(route('verification.email.confirm'), ['email_code' => $code])
            ->assertRedirect(route('verification.notice'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_phone_verification_code_can_be_confirmed(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'phone_number' => null,
            'phone_verified_at' => null,
        ]);

        $fakeSms = new class extends SmsService {
            public ?string $message = null;

            public function send(string $phoneNumber, string $message): void
            {
                $this->message = $message;
            }
        };

        $this->app->instance(SmsService::class, $fakeSms);

        $this->actingAs($user)
            ->post(route('verification.phone.update'), ['phone_number' => '081234567890'])
            ->assertRedirect();

        $this->actingAs($user)
            ->post(route('verification.phone.send'))
            ->assertRedirect()
            ->assertSessionHas('success');

        preg_match('/(\d{6})/', (string) $fakeSms->message, $matches);
        $this->assertNotEmpty($matches[1] ?? null);

        $this->actingAs($user)
            ->post(route('verification.phone.confirm'), ['phone_code' => $matches[1]])
            ->assertRedirect(route('verification.notice'));

        $this->assertTrue($user->fresh()->hasVerifiedPhone());
    }
}
