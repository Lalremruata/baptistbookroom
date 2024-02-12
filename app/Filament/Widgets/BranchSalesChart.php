<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\MainStock;
use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class BranchSalesChart extends ChartWidget
{
    protected static ?int $sort = 2;
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = null;
    protected int | string | array $columnSpan = 'half';
    protected static ?string $heading = 'Branch Sales Chart';
    public ?string $filter = 'today';

    protected function getData(): array
    {
        $branches = Branch::all();

        $datasets = [];
        $monthlyCounts = collect(); // Initialize an empty collection for monthly counts

        foreach ($branches as $branch) {
            $salesData = $this->getFilteredSalesData($branch);

            $monthlyCounts[$branch->branch_name] = $salesData
                ->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('m');
                })
                ->map(function ($month) {
                    return count($month);
                });

            $datasets[] = [
                'label' => "{$branch->branch_name}",
                'data' => $monthlyCounts[$branch->branch_name]->values(),
            ];
        }

        return [
            'datasets' => $datasets,
            'labels' => $monthlyCounts->first() ? $monthlyCounts->first()->keys()->toArray() : [],
        ];
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Today',
            'week' => 'Last week',
            'month' => 'Last month',
            'year' => 'This year',
        ];
    }

    protected function getFilteredSalesData(Branch $branch): Collection
    {
        $query = Sale::where('branch_id', $branch->id);

        switch ($this->filter) {
            case 'today':
                $query->whereDate('created_at', now()->toDateString());
                break;
            case 'week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                break;
            case 'year':
                $query->whereBetween('created_at', [now()->startOfYear(), now()->endOfYear()]);
                break;
            // Add more cases for additional filters if needed
        }

        return $query->get();
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
