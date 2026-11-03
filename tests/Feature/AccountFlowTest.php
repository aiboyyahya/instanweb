<?php

namespace Tests\Feature;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_redirects_each_role_to_the_right_area(): void
    {
        $customer = User::factory()->create([
            'email' => 'pembeli@example.com',
            'role' => 'user',
        ]);

        $this->post('/login/email', [
            'email' => 'pembeli@example.com',
            'password' => 'password',
        ])->assertRedirect('/beranda');

        auth()->logout();

        $owner = User::factory()->create([
            'email' => 'pemilik@example.com',
            'role' => 'admin',
        ]);

        $this->post('/login/email', [
            'email' => 'pemilik@example.com',
            'password' => 'password',
        ])->assertRedirect('/admin');
    }

    public function test_store_registration_page_renders_for_guests_and_members(): void
    {
        $this->get('/register/toko')
            ->assertOk()
            ->assertSee('Daftar & buat toko');

        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->get('/register/toko')
            ->assertOk()
            ->assertSee('Buat toko Anda');
    }

    public function test_guest_can_register_a_store_and_becomes_owner(): void
    {
        $response = $this->post('/register/toko', [
            'name' => 'Pemilik Baru',
            'email' => 'pemilikbaru@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'store_name' => 'Toko Baru',
            'address' => 'Jl. Contoh No. 1',
            'whatsapp' => '08123456789',
        ]);

        $response->assertRedirect('/admin');
        $this->assertAuthenticated();

        $user = User::where('email', 'pemilikbaru@example.com')->firstOrFail();
        $store = Store::where('user_id', $user->id)->firstOrFail();

        $this->assertSame('admin', $user->role);
        $this->assertSame('Toko Baru', $store->store_name);
    }

    public function test_authenticated_customer_can_upgrade_to_store_owner(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->post('/register/toko', [
                'store_name' => 'Toko Saya',
                'address' => 'Jl. Pemilik No. 2',
            ])
            ->assertRedirect(route('profil'));

        $customer->refresh();

        $this->assertSame('admin', $customer->role);
        $this->assertDatabaseHas('stores', [
            'user_id' => $customer->id,
            'store_name' => 'Toko Saya',
        ]);
    }

    public function test_profile_shows_store_cta_for_customers(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Buat Toko Sekarang')
            ->assertDontSee('Pengaturan Toko');
    }

    public function test_profile_shows_store_settings_for_owner(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Pemilik',
            'address' => 'Jl. Toko No. 3',
        ]);

        $this->actingAs($owner)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Pengaturan Toko')
            ->assertSee('Toko Pemilik')
            ->assertSee('for="store_address"', false);
    }

    public function test_owner_without_store_still_sees_store_settings_form(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);

        $this->actingAs($owner)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Pengaturan Toko')
            ->assertSee('Belum dibuat');
    }

    public function test_owner_can_update_store_settings_from_profile(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = Store::create([
            'user_id' => $owner->id,
            'store_name' => 'Toko Lama',
        ]);

        $this->actingAs($owner)
            ->put('/profil/toko', [
                'store_name' => 'Toko Baru',
                'address' => 'Jl. Baru No. 4',
                'whatsapp' => '081200000000',
            ])
            ->assertRedirect(route('profil'));

        $this->assertSame('Toko Baru', $store->fresh()->store_name);
        $this->assertSame('Jl. Baru No. 4', $store->fresh()->address);
    }

    public function test_customer_cannot_update_store_settings(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->put('/profil/toko', ['store_name' => 'Toko Liar'])
            ->assertRedirect(route('profil'));

        $this->assertDatabaseMissing('stores', ['store_name' => 'Toko Liar']);
    }
}
