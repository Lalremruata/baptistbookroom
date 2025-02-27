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
                Infolists\Components\Section::make('Item Details')->schema([
                    Infolists\Components\TextEntry::make('item.item_name')
                        ->label('Item Name'),
                    Infolists\Components\TextEntry::make('item.hsn_number')
                        ->label('HSN'),
                    Infolists\Components\TextEntry::make('item.category.category_name')
                        ->label('Category'),
                    Infolists\Components\TextEntry::make('item.subCategory.subcategory_name')
                        ->label('Sub Category'),
                ])->columns(2),
                Infolists\Components\Section::make('Stock Details')->schema([
                    Infolists\Components\TextEntry::make('quantity'),
                    Infolists\Components\TextEntry::make('cost_price'),
                    Infolists\Components\TextEntry::make('mrp'),
                    Infolists\Components\TextEntry::make('batch'),
                    Infolists\Components\TextEntry::make('barcode')
                        ->label('barcode'),
                    Infolists\Components\TextEntry::make('discount'),
                ])->columns(2),
                Infolists\Components\Section::make()->schema([
                    Infolists\Components\TextEntry::make('updated_at')
                    ->label('Recieved date'),
                ])->columns(2),
 
            ])->columns(2);
    }
}
