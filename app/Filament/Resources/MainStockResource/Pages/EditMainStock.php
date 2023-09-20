<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMainStock extends EditRecord
{
    protected static string $resource = MainStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
