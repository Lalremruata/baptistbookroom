<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSales extends BaseWidget
{
    protected static ?int $sort = 3;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()
            )
            ->columns([
                TextColumn::make('branchStock.mainStock.item.item_name'),
                TextColumn::make('quantity'),
                TextColumn::make('sale_date')
                    ->label('date')
                    ->date()
                    ->sortable(),
            ]);
    }
}
