<?php

namespace App\Filament\Resources\BranchstockResource\Pages;

use App\Filament\Resources\BranchstockResource;
use App\Models\BranchStock;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;
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
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('quantity'),
                Infolists\Components\TextEntry::make('updated_at')
                ->label('Recieved date'),
            ])->columns(2);
    }
}
