<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_render_successfully(): void
    {
        $pages = [
            '/',
            '/produk',
            '/artikel',
            '/kontak',
            '/selamatdatang',
            '/login',
            '/register',
        ];

        foreach ($pages as $page) {
            $this->get($page)->assertOk();
        }
    }

    public function test_guest_is_redirected_to_login_for_account_pages(): void
    {
        foreach (['/profile', '/pesanan', '/cart', '/checkout'] as $page) {
            $this->get($page)->assertRedirect('/login');
        }
    }

    public function test_authenticated_customer_pages_render_successfully(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $pages = [
            '/profile',
            '/pesanan',
            '/cart',
            '/ratings/create',
        ];

        foreach ($pages as $page) {
            $this->actingAs($user)->get($page)->assertOk();
        }
    }

    public function test_welcome_page_shows_admin_panel_link_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/selamatdatang')
            ->assertOk()
            ->assertSee('Buka Panel Admin');
    }

    public function test_google_login_shows_friendly_error_when_credentials_are_missing(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
        ]);

        $this->get('/google/redirect')
            ->assertRedirect('/login')
            ->assertSessionHas('error');
    }
}
