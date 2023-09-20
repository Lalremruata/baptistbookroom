<?php

namespace App\Filament\Resources\BranchStockResource\Pages;

use App\Filament\Resources\BranchStockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBranchStock extends EditRecord
{
    protected static string $resource = BranchStockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
