<?php

namespace App\Filament\Resources\StockDistributeResource\Pages;

use App\Filament\Resources\StockDistributeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStockDistribute extends ListRecords
{
    protected static string $resource = StockDistributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
