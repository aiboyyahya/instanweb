<?php

namespace Tests\Feature;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Article;
use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreIsolationTest extends TestCase
{
    use RefreshDatabase;

    private function store(User $owner, string $name): Store
    {
        return Store::create([
            'user_id' => $owner->id,
            'store_name' => $name,
        ]);
    }

    private function product(Store $store, Category $category, string $name, string $slug): Product
    {
        return Product::create([
            'store_id' => $store->id,
            'category_id' => $category->id,
            'product_name' => $name,
            'slug' => $slug,
            'description' => 'Produk uji.',
            'purchase_price' => 10000,
            'selling_price' => 15000,
            'stock' => 5,
            'status' => 'active',
            'image' => 'products/uji.jpg',
        ]);
    }

    private function article(Store $store, User $author, string $title, string $slug): Article
    {
        return Article::create([
            'store_id' => $store->id,
            'user_id' => $author->id,
            'title' => $title,
            'slug' => $slug,
            'description' => '<p>Isi artikel uji.</p>',
        ]);
    }

    public function test_beranda_searches_stores_by_keyword(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $this->store($ownerA, 'Toko Kopi Nusantara');
        $this->store($ownerB, 'Toko Lampu Hias');

        $this->get('/beranda?q=Kopi')
            ->assertOk()
            ->assertSee('Toko Kopi Nusantara')
            ->assertDontSee('Toko Lampu Hias')
            ->assertSee('Hasil pencarian');
    }

    public function test_categories_are_isolated_per_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = $this->store($ownerA, 'Toko A');
        $storeB = $this->store($ownerB, 'Toko B');

        // Nama kategori yang sama boleh dipakai toko berbeda.
        $categoryA = Category::create([
            'store_id' => $storeA->id,
            'category_name' => 'Minuman',
        ]);

        $categoryB = Category::create([
            'store_id' => $storeB->id,
            'category_name' => 'Minuman',
        ]);

        $this->assertNotSame($categoryA->id, $categoryB->id);

        $this->product($storeA, $categoryA, 'Kopi A', 'kopi-a');
        $this->product($storeB, $categoryB, 'Teh B', 'teh-b');

        $this->get(route('store.products', $storeA))
            ->assertOk()
            ->assertSee('Kopi A')
            ->assertDontSee('Teh B');

        // Filter kategori toko A tidak menampilkan kategori milik toko B.
        $this->actingAs($ownerA);
        $this->assertSame(
            [$categoryA->id],
            CategoryResource::getEloquentQuery()->pluck('id')->all(),
        );
    }

    public function test_articles_are_isolated_per_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = $this->store($ownerA, 'Toko A');
        $storeB = $this->store($ownerB, 'Toko B');

        $articleA = $this->article($storeA, $ownerA, 'Tips Toko A', 'tips-toko-a');
        $articleB = $this->article($storeB, $ownerB, 'Tips Toko B', 'tips-toko-b');

        $this->get(route('store.articles.index', $storeA))
            ->assertOk()
            ->assertSee('Tips Toko A')
            ->assertDontSee('Tips Toko B');

        $this->get(route('store.articles.show', [$storeA, $articleA]))
            ->assertOk()
            ->assertSee('Tips Toko A');

        // Artikel toko lain tidak bisa dibuka melalui storefront toko A.
        $this->get(route('store.articles.show', [$storeA, $articleB]))
            ->assertNotFound();

        $this->actingAs($ownerA);
        $this->assertSame(
            [$articleA->id],
            ArticleResource::getEloquentQuery()->pluck('id')->all(),
        );
    }

    public function test_store_contact_page_only_shows_that_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = Store::create([
            'user_id' => $ownerA->id,
            'store_name' => 'Toko A',
            'address' => 'Alamat Toko A',
            'whatsapp' => '0811111111',
        ]);

        Store::create([
            'user_id' => $ownerB->id,
            'store_name' => 'Toko B',
            'address' => 'Alamat Toko B',
            'whatsapp' => '0822222222',
        ]);

        $this->get(route('store.contact', $storeA))
            ->assertOk()
            ->assertSee('Alamat Toko A')
            ->assertDontSee('Alamat Toko B');
    }

    public function test_store_contact_page_uses_only_the_current_store_logo(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = Store::create([
            'user_id' => $ownerA->id,
            'store_name' => 'Toko A',
            'logo' => 'logos/toko-a.jpg',
        ]);

        $storeB = Store::create([
            'user_id' => $ownerB->id,
            'store_name' => 'Toko B',
            'logo' => 'logos/toko-b.jpg',
        ]);

        $this->get(route('store.contact', $storeA))
            ->assertOk()
            ->assertSee('logos/toko-a.jpg', false)
            ->assertDontSee('logos/toko-b.jpg', false);

        $this->get(route('store.contact', $storeB))
            ->assertOk()
            ->assertSee('logos/toko-b.jpg', false)
            ->assertDontSee('logos/toko-a.jpg', false);
    }

    public function test_store_contact_page_without_logo_does_not_use_another_store_logo(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        Store::create([
            'user_id' => $ownerA->id,
            'store_name' => 'Toko A',
            'logo' => 'logos/toko-a.jpg',
        ]);

        $storeB = Store::create([
            'user_id' => $ownerB->id,
            'store_name' => 'Toko B',
        ]);

        $this->get(route('store.contact', $storeB))
            ->assertOk()
            ->assertSee('Toko B')
            ->assertDontSee('logos/toko-a.jpg', false);
    }

    public function test_navigation_points_to_the_current_store_content(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = $this->store($owner, 'Toko Navigasi');

        $this->get(route('store.show', $store))
            ->assertOk()
            ->assertSee(route('store.articles.index', $store), false)
            ->assertSee(route('store.contact', $store), false)
            ->assertSee(route('store.products', $store), false);
    }

    public function test_cart_navigation_keeps_the_store_context(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = $this->store($ownerA, 'Toko Pertama');
        $storeB = $this->store($ownerB, 'Toko Kedua');

        $categoryB = Category::create([
            'store_id' => $storeB->id,
            'category_name' => 'Kategori B',
        ]);

        $productB = $this->product($storeB, $categoryB, 'Produk Toko Kedua', 'produk-toko-kedua');

        $buyer = User::factory()->create(['role' => 'user']);

        $this->actingAs($buyer)
            ->post(route('addToCart'), [
                'product_id' => $productB->id,
                'quantity' => 1,
            ])
            ->assertRedirect(route('cart'));

        $this->get(route('cart'))
            ->assertOk()
            ->assertSee('Toko Kedua')
            ->assertSee(route('store.show', $storeB), false)
            ->assertSee(route('store.products', $storeB), false)
            ->assertSee(route('store.articles.index', $storeB), false)
            ->assertSee(route('store.contact', $storeB), false);

        $this->assertNotSame($storeA->id, $storeB->id);
    }

    public function test_cart_items_are_isolated_per_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = $this->store($ownerA, 'Toko A');
        $storeB = $this->store($ownerB, 'Toko B');

        $categoryA = Category::create([
            'store_id' => $storeA->id,
            'category_name' => 'Kategori A',
        ]);

        $categoryB = Category::create([
            'store_id' => $storeB->id,
            'category_name' => 'Kategori B',
        ]);

        $productA = $this->product($storeA, $categoryA, 'Produk Eksklusif A', 'produk-eksklusif-a');
        $productB = $this->product($storeB, $categoryB, 'Produk Eksklusif B', 'produk-eksklusif-b');

        $buyer = User::factory()->create(['role' => 'user']);

        $this->actingAs($buyer)
            ->post(route('addToCart'), [
                'product_id' => $productA->id,
                'quantity' => 1,
            ]);

        $this->post(route('addToCart'), [
            'product_id' => $productB->id,
            'quantity' => 1,
        ]);

        // Toko terakhir yang dipakai adalah toko B.
        $this->get(route('cart'))
            ->assertOk()
            ->assertSee('Produk Eksklusif B')
            ->assertDontSee('Produk Eksklusif A');

        // Saat kembali ke toko A, keranjang toko A yang ditampilkan.
        $this->get(route('store.show', $storeA))->assertOk();

        $this->get(route('cart'))
            ->assertOk()
            ->assertSee('Produk Eksklusif A')
            ->assertDontSee('Produk Eksklusif B');
    }

    public function test_profile_navigation_uses_the_owner_store(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = $this->store($owner, 'Toko Profil');

        $this->actingAs($owner)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee('Toko Profil')
            ->assertSee(route('store.show', $store), false)
            ->assertSee(route('store.products', $store), false)
            ->assertSee(route('store.articles.index', $store), false)
            ->assertSee(route('store.contact', $store), false);
    }

    public function test_profile_navigation_uses_the_last_store_for_customers(): void
    {
        $owner = User::factory()->create(['role' => 'admin']);
        $store = $this->store($owner, 'Toko Terakhir');

        $buyer = User::factory()->create(['role' => 'user']);

        // Pembeli mengunjungi storefront terlebih dahulu.
        $this->get(route('store.show', $store))->assertOk();

        $this->actingAs($buyer)
            ->get(route('profil'))
            ->assertOk()
            ->assertSee('Toko Terakhir')
            ->assertSee(route('store.show', $store), false)
            ->assertSee(route('store.products', $store), false)
            ->assertSee(route('store.articles.index', $store), false)
            ->assertSee(route('store.contact', $store), false);
    }

    public function test_transactions_are_isolated_per_store(): void
    {
        $ownerA = User::factory()->create(['role' => 'admin']);
        $ownerB = User::factory()->create(['role' => 'admin']);

        $storeA = $this->store($ownerA, 'Toko A');
        $storeB = $this->store($ownerB, 'Toko B');

        $categoryA = Category::create(['store_id' => $storeA->id, 'category_name' => 'Kategori A']);
        $categoryB = Category::create(['store_id' => $storeB->id, 'category_name' => 'Kategori B']);

        $productA = $this->product($storeA, $categoryA, 'Produk A', 'produk-a');
        $productB = $this->product($storeB, $categoryB, 'Produk B', 'produk-b');

        $transactionA = Transaction::create([
            'order_code' => 'INV-A',
            'customer_id' => $ownerA->id,
            'address' => 'Alamat A',
            'status' => 'pending',
            'total' => 15000,
            'payment_method' => 'manual',
            'payment_status' => 'pending',
        ]);

        $transactionB = Transaction::create([
            'order_code' => 'INV-B',
            'customer_id' => $ownerB->id,
            'address' => 'Alamat B',
            'status' => 'pending',
            'total' => 15000,
            'payment_method' => 'manual',
            'payment_status' => 'pending',
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionA->id,
            'product_id' => $productA->id,
            'price' => 15000,
            'quantity' => 1,
        ]);

        TransactionItem::create([
            'transaction_id' => $transactionB->id,
            'product_id' => $productB->id,
            'price' => 15000,
            'quantity' => 1,
        ]);

        $this->actingAs($ownerA);
        $this->assertSame(
            [$transactionA->id],
            TransactionResource::getEloquentQuery()->pluck('id')->all(),
        );
    }
}
