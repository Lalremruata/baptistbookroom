<?php

namespace App\Filament\Resources\BranchStockResource\Pages;

use App\Filament\Resources\BranchStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBranchStocks extends ListRecords
{
    protected static string $resource = BranchStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
