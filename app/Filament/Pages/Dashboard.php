<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Articles\ArticleResource;
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
        $store = Store::first();
        $userName = auth()->user()?->name ?? 'Admin';
        $firstName = str($userName)->trim()->before(' ')->toString();

        return [
            'firstName' => $firstName !== '' ? $firstName : 'Admin',
            'storeName' => $store?->store_name,
            'storeUrl' => route('home'),
            'quickActions' => [
                [
                    'label' => 'Tambah Produk',
                    'description' => 'Publikasikan produk baru',
                    'url' => ProductResource::getUrl('create'),
                    'icon' => 'heroicon-o-plus-circle',
                ],
                [
                    'label' => 'Lihat Pesanan',
                    'description' => 'Pantau status transaksi',
                    'url' => TransactionResource::getUrl('index'),
                    'icon' => 'heroicon-o-shopping-bag',
                ],
                [
                    'label' => 'Tulis Artikel',
                    'description' => 'Bagikan informasi toko',
                    'url' => ArticleResource::getUrl('create'),
                    'icon' => 'heroicon-o-document-plus',
                ],
                [
                    'label' => 'Pengaturan Toko',
                    'description' => 'Identitas & pembayaran',
                    'url' => Pengaturan::getUrl(),
                    'icon' => 'heroicon-o-cog-6-tooth',
                ],
            ],
        ];
    }
}
