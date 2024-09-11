<?php

namespace App\Filament\Resources\MainStockResource\Pages;

use App\Filament\Resources\MainStockResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMainStock extends ViewRecord
{
    protected static string $resource = MainStockResource::class;
    protected function getActions(): array
    {
        return [];
    }
}
