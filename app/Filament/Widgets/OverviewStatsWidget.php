<?php

namespace App\Filament\Widgets;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OverviewStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Total Produk UMKM', Product::count())
                ->description('Produk yang dikelola di instanweb')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->chart($this->dailyTrend(Product::class))
                ->chartColor('primary'),

            Stat::make('Kategori Produk', Category::count())
                ->description('Kategori untuk memudahkan pencarian')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info')
                ->chart($this->dailyTrend(Category::class))
                ->chartColor('info'),

            Stat::make('Total Pesanan', Transaction::count())
                ->description('Transaksi pelanggan dari berbagai daerah')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('success')
                ->chart($this->dailyTrend(Transaction::class))
                ->chartColor('success'),
        ];
    }

    /**
     * Tren jumlah data baru selama tujuh hari terakhir.
     *
     * @param  class-string<\Illuminate\Database\Eloquent\Model>  $model
     * @return array<int, int>
     */
    private function dailyTrend(string $model): array
    {
        return collect(range(6, 0))
            ->map(fn (int $daysAgo): int => $model::query()
                ->whereDate('created_at', Carbon::now()->subDays($daysAgo)->toDateString())
                ->count())
            ->all();
    }
}
