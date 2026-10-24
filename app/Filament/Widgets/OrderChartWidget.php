<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChartWidget extends ChartWidget
{
    protected ?string $heading = 'Statistik Pesanan';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

  protected function getData(): array
{
    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $count = Transaction::whereDate('created_at', $date->toDateString())->count();
        $data[] = $count;
    }

    return [
        'datasets' => [
            [
                'label' => 'Pesanan',
                'data' => $data,
                'backgroundColor' => 'rgba(75, 192, 192, 0.5)',
                'borderColor' => 'rgb(75, 192, 192)',
                'borderWidth' => 1,
            ],
        ],
        'labels' => ['6 hari lalu', '5 hari lalu', '4 hari lalu', '3 hari lalu', '2 hari lalu', 'Kemarin', 'Hari ini'],
    ];
}

protected function getType(): string
{
    return 'bar'; // ubah dari 'line' ke 'bar'
}

} 