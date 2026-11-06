<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OverviewStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $products = $this->productQuery();
        $categories = $this->categoryQuery();
        $orders = $this->orderQuery();

        return [
            Stat::make('Total Produk', (clone $products)->count())
                ->description('Produk di toko Anda')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart($this->dailyTrend($products))
                ->chartColor('primary'),

            Stat::make('Kategori', (clone $categories)->count())
                ->description('Pengelompokan produk')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info')
                ->chart($this->dailyTrend($categories))
                ->chartColor('info'),

            Stat::make('Pesanan', (clone $orders)->count())
                ->description('Transaksi masuk')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart($this->dailyTrend($orders))
                ->chartColor('success'),
        ];
    }

    private function storeId(): ?int
    {
        return auth()->user()?->store?->id;
    }

    private function productQuery(): Builder
    {
        return Product::query()->when(
            $this->storeId(),
            fn (Builder $query, int $storeId) => $query->where('store_id', $storeId),
        );
    }

    private function categoryQuery(): Builder
    {
        return Category::query()->when(
            $this->storeId(),
            fn (Builder $query, int $storeId) => $query->where('store_id', $storeId),
        );
    }

    private function orderQuery(): Builder
    {
        return Transaction::query()->when(
            $this->storeId(),
            fn (Builder $query, int $storeId) => $query->whereHas(
                'items.product',
                fn (Builder $query) => $query->where('store_id', $storeId),
            ),
        );
    }

    /**
     * Tren jumlah data baru selama tujuh hari terakhir.
     *
     * @param  \Illuminate\Database\Eloquent\Builder<*>  $query
     * @return array<int, int>
     */
    private function dailyTrend(Builder $query): array
    {
        return collect(range(6, 0))
            ->map(fn (int $daysAgo): int => (clone $query)
                ->whereDate('created_at', Carbon::now()->subDays($daysAgo)->toDateString())
                ->count())
            ->all();
    }
}
