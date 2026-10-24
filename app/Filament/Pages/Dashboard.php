<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\OverviewStatsWidget::class,
            \App\Filament\Widgets\OrderChartWidget::class,
            \App\Filament\Widgets\OrderLineChartWidget::class,
            \App\Filament\Widgets\LatestOrdersWidget::class,
        ];
    }
}
