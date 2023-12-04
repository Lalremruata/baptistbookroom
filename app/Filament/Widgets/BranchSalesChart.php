<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\MainStock;
use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;

class BranchSalesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static ?string $heading = 'Branch Sales Chart';

    protected function getData(): array
    {
        $branches = Branch::all(); // Assuming you have a 'Branch' model

        $datasets = [];

        foreach ($branches as $branch) {
            $salesData = Sale::where('branch_id', $branch->id)
                ->whereBetween('created_at', [
                    now()->startOfYear(),
                    now()->endOfYear(),
                ])
                ->get();

            $monthlyCounts = $salesData
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('m');
                })
                ->map(function ($month) {
                    return count($month);
                });

            $datasets[] = [
                'label' => "{$branch->branch_name}",
                'data' => $monthlyCounts->values(),
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $monthlyCounts->keys()->toArray(),
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }
}
