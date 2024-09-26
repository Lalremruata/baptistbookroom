<?php

namespace App\Filament\Resources\StockDistributeResource\Pages;

use App\Filament\Resources\StockDistributeResource;
use App\Models\StockDistribute;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\Actions\Action;
use Filament\Support\Enums\MaxWidth;
class ViewStockDistribute extends ViewRecord
{
    protected static string $resource = StockDistributeResource::class;
    public function getTitle(): string | Htmlable
    {
        /** @var StockDistribute */
        $record = $this->getRecord();

        return $record->item->item_name;
    }
    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
        ->schema([
            Section::make('Item Received')
            ->headerActions([
                Action::make('Stock Distribute List')
                ->url(StockDistributeResource::getUrl('index'))
                ->icon('heroicon-o-arrow-long-right')
                ->link(),
            ])
            ->description('Details of item received')
            ->columns([
                'sm' => 3,
                'xl' => 6,
                '2xl' => 8,
            ])
            ->schema([
                // Split::make([
                //     Grid::make(2)
                //     ->schema([
                        Infolists\Components\TextEntry::make('item.item_name')
                        ->label('Item Received'),
                        Infolists\Components\TextEntry::make('quantity')
                        ->label('Quantity Received'),
                        Infolists\Components\TextEntry::make('created_at')
                        ->label('Received date'),
                    ])
                    // ])

            // ])
            ]);

    }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }
}
