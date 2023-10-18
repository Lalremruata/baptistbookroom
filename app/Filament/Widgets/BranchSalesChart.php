<?php

namespace App\Filament\Widgets;

use App\Models\MainStock;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class BranchSalesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Branch Sales Chart';

    protected function getData(): array
    {
        $data = Trend::model(MainStock::class)
        ->between(
            start: now()->startOfYear(),
            end: now()->endOfYear(),
        )
        ->perMonth()
        ->count();
        return [
            'datasets' => [
                [
                    'label' => 'Branch Sales',
                    'data' => $data->map(fn (TrendValue $value) => $value->aggregate),
                ],
            ],
            'labels' => $data->map(fn (TrendValue $value) => $value->date),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
