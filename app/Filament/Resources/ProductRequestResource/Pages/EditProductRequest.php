<?php

namespace App\Filament\Resources\ProductRequestResource\Pages;

use App\Filament\Resources\ProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductRequest extends EditRecord
{
    protected static string $resource = ProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
