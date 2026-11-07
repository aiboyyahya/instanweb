<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Resources\Categories\CategoryResource;
use App\Filament\Resources\Products\ProductResource;
use App\Filament\Resources\Transactions\TransactionResource;
use App\Models\Store;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    protected string $view = 'filament.pages.dashboard';

    public function getHeading(): string | Htmlable
    {
        return 'Ringkasan Toko';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\OverviewStatsWidget::class,
            \App\Filament\Widgets\OrderChartWidget::class,
            \App\Filament\Widgets\OrderLineChartWidget::class,
            \App\Filament\Widgets\LatestOrdersWidget::class,
        ];
    }

    protected function getViewData(): array
    {
        $store   = auth()->user()?->store ?: Store::first();
        $userName = auth()->user()?->name ?? 'Admin';
        $firstName = str($userName)->trim()->before(' ')->toString();

        // Cek kondisi toko untuk menandai tutorial
        $hasStoreName   = !empty($store?->store_name);
        $hasProducts    = \App\Models\Product::query()
            ->when($store?->id, fn ($q, int $id) => $q->where('store_id', $id))
            ->count() > 0;
        $hasCategories  = \App\Models\Category::query()
            ->when($store?->id, fn ($q, int $id) => $q->where('store_id', $id))
            ->count() > 0;
        $hasOrders      = \App\Models\Transaction::query()
            ->when($store?->id, fn ($q, int $id) => $q->whereHas('items.product', fn ($q2) => $q2->where('store_id', $id)))
            ->count() > 0;
        $hasArticles    = \App\Models\Article::query()
            ->when($store?->id, fn ($q, int $id) => $q->where('store_id', $id))
            ->count() > 0;

        $allDone = $hasStoreName && $hasProducts && $hasCategories && $hasOrders;

        $tutorialSteps = [
            [
                'label' => 'Lengkapi pengaturan toko',
                'hint'  => 'Nama, alamat, WhatsApp, dan logo toko.',
                'url'   => Pengaturan::getUrl(),
                'done'  => $hasStoreName,
            ],
            [
                'label' => 'Buat kategori pertama',
                'hint'  => 'Kelompokkan produk agar pelanggan mudah mencari.',
                'url'   => CategoryResource::getUrl('create'),
                'done'  => $hasCategories,
            ],
            [
                'label' => 'Tambah produk',
                'hint'  => 'Unggah foto, isi harga dan stok.',
                'url'   => ProductResource::getUrl('create'),
                'done'  => $hasProducts,
            ],
            [
                'label' => 'Proses pesanan masuk',
                'hint'  => 'Pantau dan update status pesanan pelanggan.',
                'url'   => TransactionResource::getUrl('index'),
                'done'  => $hasOrders,
            ],
            [
                'label' => 'Tulis artikel (opsional)',
                'hint'  => 'Ceritakan cerita atau tips tentang produk Anda.',
                'url'   => ArticleResource::getUrl('create'),
                'done'  => $hasArticles,
            ],
        ];

        return [
            'firstName'     => $firstName !== '' ? $firstName : 'Admin',
            'storeName'     => $store?->store_name,
            'storeUrl'      => $store ? route('store.show', $store) : route('home'),
            'referralUrl'   => $store?->referral_url,
            'tutorialSteps' => $allDone ? [] : $tutorialSteps,
            'quickActions'  => [
                [
                    'label'       => 'Produk',
                    'description' => 'Tambah atau kelola produk',
                    'url'         => ProductResource::getUrl('create'),
                    'icon'        => 'heroicon-o-plus-circle',
                ],
                [
                    'label'       => 'Pesanan',
                    'description' => 'Pantau status transaksi',
                    'url'         => TransactionResource::getUrl('index'),
                    'icon'        => 'heroicon-o-shopping-bag',
                ],
                [
                    'label'       => 'Kategori',
                    'description' => 'Kelompokkan produk',
                    'url'         => CategoryResource::getUrl('create'),
                    'icon'        => 'heroicon-o-tag',
                ],
                [
                    'label'       => 'Pengaturan',
                    'description' => 'Identitas toko & kontak',
                    'url'         => Pengaturan::getUrl(),
                    'icon'        => 'heroicon-o-cog-6-tooth',
                ],
                [
                    'label'       => 'Artikel',
                    'description' => 'Ceritakan produk Anda',
                    'url'         => ArticleResource::getUrl('create'),
                    'icon'        => 'heroicon-o-document-plus',
                ],
            ],
        ];
    }
}
