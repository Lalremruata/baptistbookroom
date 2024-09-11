<?php

namespace App\Filament\Resources\BranchstockResource\Pages;

use App\Filament\Resources\BranchstockResource;
use App\Models\BranchStock;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewBranchStock extends ViewRecord
{
    protected static string $resource = BranchstockResource::class;
    public function getTitle(): string | Htmlable
    {
        /** @var BranchStock */
        $record = $this->getRecord();

        return $record->item->item_name;
    }
    protected function getActions(): array
    {
        return [];
    }
}
