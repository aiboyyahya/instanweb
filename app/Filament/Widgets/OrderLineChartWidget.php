<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class OrderLineChartWidget extends ChartWidget
{
    protected ?string $heading = 'Tren Pesanan';

    protected ?string $description = 'Perbandingan volume pesanan harian selama sepekan.';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected string $color = 'info';

    protected function getData(): array
    {
        $data = [];
        $orders = $this->orderQuery();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = (clone $orders)->whereDate('created_at', $date->toDateString())->count();
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => $data,
                    'borderColor' => '#1e56fc',
                    'backgroundColor' => 'rgba(30, 86, 252, 0.14)',
                    'pointBackgroundColor' => '#1e56fc',
                    'pointBorderColor' => '#ffffff',
                    'pointRadius' => 3,
                    'fill' => true,
                    'tension' => 0.35,
                ],
            ],
            'labels' => collect(range(6, 0))
                ->map(fn (int $daysAgo): string => Carbon::now()->subDays($daysAgo)->locale('id')->translatedFormat('d M'))
                ->all(),
        ];
    }

    /**
     * Hanya hitung pesanan yang memuat produk milik toko yang sedang masuk.
     */
    private function orderQuery(): Builder
    {
        return Transaction::query()->when(
            auth()->user()?->store?->id,
            fn (Builder $query, int $storeId) => $query->whereHas(
                'items.product',
                fn (Builder $query) => $query->where('store_id', $storeId),
            ),
        );
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
        ];
    }
}
