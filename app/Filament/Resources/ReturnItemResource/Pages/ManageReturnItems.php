<?php

namespace App\Filament\Resources\ReturnItemResource\Pages;

use App\Filament\Resources\ReturnItemResource;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageReturnItems extends ManageRecords
{
    protected static string $resource = ReturnItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
