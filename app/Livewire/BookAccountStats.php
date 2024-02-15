<?php

namespace App\Livewire;

use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BookAccountStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total items', Item::count())
            // ->description('32k increase')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),
        ];
    }
}
