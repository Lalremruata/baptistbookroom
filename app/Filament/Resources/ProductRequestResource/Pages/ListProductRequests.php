<?php

namespace App\Filament\Resources\ProductRequestResource\Pages;

use App\Filament\Resources\ProductRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductRequests extends ListRecords
{
    protected static string $resource = ProductRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
