<?php

namespace App\Filament\Resources\StockDistributeResource\Pages;

use App\Filament\Resources\StockDistributeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStockDistribute extends EditRecord
{
    protected static string $resource = StockDistributeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
