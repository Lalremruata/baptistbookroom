<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockDistributeResource\Pages;
use App\Models\StockDistribute;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;

class StockDistributeResource extends Resource
{
    protected static ?string $model = StockDistribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Distribute Report';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 3;

    public static function canCreate(): bool
    {
        return 0;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_type=='1';
    }

    // public static function form(Form $form): Form
    // {

    //     return $form
    //         ->schema([
    //             Card::make()
    //             ->schema([
    //                 Forms\Components\Select::make('branch_id')
    //                 ->relationship('branch', 'branch_name')
    //                 ->required(),
    //             ])->columns(3),


    //             Card::make()
    //             ->schema([
    //                 Repeater::make('stockDistributeItem')
    //                 ->relationship()
    //                 ->schema([
    //                     Forms\Components\Select::make('item_id')
    //                     ->live()
    //                     ->searchable()
    //                     ->options(Item::query()->pluck('item_name', 'id'))
    //                     // ->afterStateUpdated(fn(callable $set) => $set('quantity', null))
    //                     ->required(),
    //                     Forms\Components\TextInput::make('quantity')
    //                     ->live()
    //                     ->hint(function(Get $get){
    //                         $itemId = $get('item_id');
    //                         if ($itemId) {
    //                             $result=MainStock::where('item_id',$itemId)->select('quantity')->first();
    //                             $quantity = $result->quantity;
    //                             return 'quantity available: '.$quantity;
    //                         }
    //                         return null;
    //                     })
    //                     ->hintColor('primary')
    //                     ->required()
    //                     ->integer(),


    //                 ])->columns(2)
    //             ])
    //         ]);
    // }


    public static function table(Table $table): Table
    {
        return $table
        ->striped()
            ->columns([
                TextColumn::make('')
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
                    ->weight(FontWeight::Bold)
                    ->label('quantities')
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->money('inr'),
                TextColumn::make('mrp')
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->money('inr'),
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
                    ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
                ->headerActions([
                    ExportAction::make()->exports([
                        ExcelExport::make()->fromTable(),
                        ])
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
            // 'create' => Pages\CreateStockDistribute::route('/create'),
            // 'edit' => Pages\EditStockDistribute::route('/{record}/edit'),
        ];
    }
}
