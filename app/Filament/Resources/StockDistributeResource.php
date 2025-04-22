<?php

namespace App\Filament\Resources;

use App\Filament\Exports\StockDistributeExporter;
use App\Filament\Resources\StockDistributeResource\Pages;
use App\Models\StockDistribute;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder As QueryBuilder;

class StockDistributeResource extends Resource
{
    protected static ?string $model = StockDistribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationLabel = 'Distribute Report';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return 0;
    }

    public static function shouldRegisterNavigation(): bool
    {
        // return auth()->user()->user_type=='1';
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->user_type == '1') {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            return parent::getEloquentQuery()->where('branch_id', auth()->user()->branch_id);
        }
    }

    public static function table(Table $table): Table
    {
        return $table
        ->striped()
            ->columns([
                TextColumn::make('*')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('mainStock.item.item_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('branch.branch_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->summarize(Sum::make()->label('Total'))
                    ->weight(FontWeight::Bold)
                    ->label('quantities')
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->money('inr')
                    ->summarize(Summarizer::make()
                        ->label('Total')
                        ->using(fn(QueryBuilder $query): float => $query->get()->sum(fn($row) => $row->cost_price * $row->quantity))
                    ),
                TextColumn::make('mrp')
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->money('inr')
                    ->summarize(Summarizer::make()
                    ->label('Total')
                    ->using(fn(QueryBuilder $query): float => $query->get()->sum(fn($row) => $row->mrp * $row->quantity))
                ),
                TextColumn::make('batch')
                    ->weight(FontWeight::Bold),
                TextColumn::make('mainStock.barcode')
                    ->weight(FontWeight::Bold)
                    ->label('Bar Code')
                    ->searchable(),
                TextColumn::make('created_at')
                     ->label('Transfer date')
                    ->date()
                    ->sortable(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                ->form([
                    DatePicker::make('from'),
                    DatePicker::make('to'),
                ])->columns(2)
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['from'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                        )
                        ->when(
                            $data['to'],
                            fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                        );
                }),
                SelectFilter::make('branch')
                    ->relationship('branch','branch_name')
                    ->hidden(! auth()->user()->user_type=='1')
                    ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
                ->headerActions([
                    ExportAction::make()
                        ->exporter(StockDistributeExporter::class)
                        ->formats([
                            ExportFormat::Xlsx,
                        ])
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
                    ], position: HeaderActionsPosition::Bottom)
                ->paginated([25, 50, 100, 'all']);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockDistribute::route('/'),
            'view' => Pages\ViewStockDistribute::route('/{record}'),
            'create' => Pages\CreateStockDistribute::route('/create'),
            'edit' => Pages\EditStockDistribute::route('/{record}/edit'),
        ];
    }
}
