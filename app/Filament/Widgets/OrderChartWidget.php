<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Pesanan 7 Hari Terakhir';

    protected ?string $description = 'Jumlah pesanan yang masuk setiap hari.';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected string $color = 'primary';

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
                    'backgroundColor' => 'rgba(30, 86, 252, 0.72)',
                    'borderColor' => '#1e56fc',
                    'borderWidth' => 1,
                    'borderRadius' => 6,
                    'maxBarThickness' => 34,
                ],
            ],
            'labels' => collect(range(6, 0))
                ->map(fn (int $daysAgo): string => Carbon::now()->subDays($daysAgo)->locale('id')->translatedFormat('d M'))
                ->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
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
