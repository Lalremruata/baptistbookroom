<?php

namespace App\Filament\Widgets;

use App\Models\Sale;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSales extends BaseWidget
{
    protected static ?int $sort = 3;
    protected static bool $isLazy = false;
    protected static ?string $pollingInterval = null;
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sale::query()->latest('created_at') // Sorts by created_at in descending order
                ->limit(20)
            )
            ->columns([
                TextColumn::make('')
                ->weight(FontWeight::Bold)
                ->rowIndex(),
                TextColumn::make('branchStock.mainStock.item.item_name'),
                TextColumn::make('branchStock.branch.branch_name'),
                TextColumn::make('quantity'),
                TextColumn::make('created_at')
                    ->label('date')
                    ->date()
                    ->sortable(),
            ])
            ->paginated(false)
            ->defaultSort('created_at', 'desc');
    }
}
