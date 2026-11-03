<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Panel Filament menolak pengguna non-FilamentUser di luar APP_ENV=local.
        // Aplikasi berjalan pada APP_ENV=local, jadi uji render disamakan.
        config(['app.env' => 'local']);
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_pages_render_successfully(): void
    {
        Store::create([
            'store_name' => 'Toko Uji',
        ]);

        $pages = [
            '/admin',
            '/admin/products',
            '/admin/products/create',
            '/admin/categories',
            '/admin/categories/create',
            '/admin/transactions',
            '/admin/articles',
            '/admin/articles/create',
            '/admin/pengaturan',
        ];

        $admin = $this->admin();

        foreach ($pages as $page) {
            $this->actingAs($admin)->get($page)->assertOk();
        }

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Ringkasan toko')
            ->assertSee('Lihat Toko');

        $this->actingAs($admin)
            ->get('/admin/products')
            ->assertOk()
            ->assertSee('Kelola katalog produk');

        $this->actingAs($admin)
            ->get('/admin/pengaturan')
            ->assertOk()
            ->assertSee('Identitas Toko')
            ->assertSee('Simpan Perubahan');
    }

    public function test_admin_edit_pages_render_with_existing_records(): void
    {
        $admin = $this->admin();
        $category = Category::create(['category_name' => 'Makanan']);
        $product = Product::create([
            'product_name' => 'Keripik',
            'slug' => 'keripik',
            'description' => 'Keripik renyah.',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 4,
            'status' => 'active',
            'category_id' => $category->id,
            'image' => 'products/contoh.jpg',
        ]);
        $transaction = Transaction::create([
            'order_code' => 'INV-001',
            'customer_id' => $admin->id,
            'address' => 'Jl. Uji No. 1',
            'status' => 'pending',
            'total' => 15000,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
        ]);

        $this->actingAs($admin)->get("/admin/products/{$product->id}/edit")->assertOk();
        $this->actingAs($admin)->get("/admin/products/{$product->id}")->assertOk();
        $this->actingAs($admin)->get("/admin/categories/{$category->id}/edit")->assertOk();
        $this->actingAs($admin)->get("/admin/transactions/{$transaction->id}/edit")->assertOk();
    }

    public function test_non_admin_cannot_open_admin_panel(): void
    {
        $customer = User::factory()->create(['role' => 'user']);

        $this->actingAs($customer)
            ->get('/admin')
            ->assertForbidden();
    }
}
