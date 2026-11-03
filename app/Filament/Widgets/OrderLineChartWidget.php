<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
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

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $data[] = Transaction::whereDate('created_at', $date->toDateString())->count();
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
