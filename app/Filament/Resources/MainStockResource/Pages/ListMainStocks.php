<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMainStocks extends ListRecords
{
    protected static string $resource = MainStockResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
