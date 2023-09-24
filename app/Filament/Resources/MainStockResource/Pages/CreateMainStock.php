<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMainStock extends CreateRecord
{
    protected static string $resource = MainStockResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
