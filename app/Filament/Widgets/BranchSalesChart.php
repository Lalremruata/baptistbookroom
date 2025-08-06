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
        $allLabels = collect(); // To store all possible labels across branches

        foreach ($branches as $branch) {
            $salesData = $this->getFilteredSalesData($branch);

            // Group data based on the selected filter
            $groupedData = $this->groupDataByFilter($salesData);

            // Collect all labels to ensure consistency across branches
            $allLabels = $allLabels->merge($groupedData->keys())->unique()->sort();

            $datasets[] = [
                'label' => $branch->branch_name,
                'data' => $groupedData,
            ];
        }

        // Ensure all datasets have the same labels
        $normalizedDatasets = $this->normalizeDatasets($datasets, $allLabels);

        return [
            'datasets' => $normalizedDatasets,
            'labels' => $allLabels->values()->toArray(),
        ];
    }

    /**
     * Group sales data based on the current filter selection
     * This method demonstrates how different time periods require different grouping strategies
     */
    protected function groupDataByFilter(Collection $salesData): Collection
    {
        switch ($this->filter) {
            case 'today':
                // For today, group by hour to show hourly sales pattern
                return $salesData
                    ->groupBy(function ($sale) {
                        return Carbon::parse($sale->created_at)->format('H:00'); // Group by hour
                    })
                    ->map(function ($hourSales) {
                        return $hourSales->sum('total_amount'); // Sum the sales amount, not just count
                    });

            case 'week':
                // For week, group by day name
                return $salesData
                    ->groupBy(function ($sale) {
                        return Carbon::parse($sale->created_at)->format('l'); // Day name (Monday, Tuesday, etc.)
                    })
                    ->map(function ($daySales) {
                        return $daySales->sum('total_amount');
                    });

            case 'month':
                // For month, group by day of month
                return $salesData
                    ->groupBy(function ($sale) {
                        return Carbon::parse($sale->created_at)->format('j'); // Day of month (1, 2, 3...)
                    })
                    ->map(function ($daySales) {
                        return $daySales->sum('total_amount');
                    });

            case 'year':
                // For year, group by month name
                return $salesData
                    ->groupBy(function ($sale) {
                        return Carbon::parse($sale->created_at)->format('M'); // Month abbreviation (Jan, Feb, etc.)
                    })
                    ->map(function ($monthSales) {
                        return $monthSales->sum('total_amount');
                    });

            default:
                return collect();
        }
    }

    /**
     * Normalize datasets to ensure all branches have data for all labels
     * This prevents chart rendering issues when different branches have different data points
     */
    protected function normalizeDatasets(array $datasets, Collection $allLabels): array
    {
        return array_map(function ($dataset) use ($allLabels) {
            $normalizedData = [];

            foreach ($allLabels as $label) {
                // If this branch has data for this label, use it; otherwise, use 0
                $normalizedData[] = $dataset['data'][$label] ?? 0;
            }

            return [
                'label' => $dataset['label'],
                'data' => $normalizedData,
            ];
        }, $datasets);
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
