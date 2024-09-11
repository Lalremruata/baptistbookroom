<?php

namespace App\Filament\Resources\MainStockResource\Widgets;

use App\Filament\Resources\MainStockResource;
use App\Models\Item;
use App\Models\MainStock;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class MainStockValueOverview extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        $priceDifference = MainStock::select(DB::raw('SUM(mrp) - SUM(cost_price) as total_difference'))
        ->value('total_difference');
            return [
                Stat::make('Main Stock Value', $priceDifference)
                    ->description('rupees')
                    ->descriptionIcon('heroicon-m-currency-rupee')
                    ->chart([7, 2, 10, 3, 15, 4, 17])
                    ->color('success'),
            ];
    }
}
