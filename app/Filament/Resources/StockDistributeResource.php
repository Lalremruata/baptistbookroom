<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockDistributeResource\Pages;
use App\Models\StockDistribute;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Carbon;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Filament\Tables\Actions\HeaderActionsPosition;

class StockDistributeResource extends Resource
{
    protected static ?string $model = StockDistribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Main Stocks';
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
            ->columns([
                Tables\Columns\TextColumn::make('item.item_name')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                ->label('quantities')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                     ->label('Transfer date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                ->form([DatePicker::make('date')])
                // ...
                ->indicateUsing(function (array $data): ?string {
                    if (! $data['date']) {
                        return null;
                    }
                return 'Created at ' . Carbon::parse($data['date'])->toFormattedDateString();
                }),
                SelectFilter::make('branch')
                    ->relationship('branch','branch_name')
                ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)->filtersFormWidth('4xl')
                ->headerActions([
                    ExportAction::make()->exports([
                        ExcelExport::make()->fromTable(),
                        // ExcelExport::make('form')->fromForm(),
                    ])
                ], position: HeaderActionsPosition::Bottom);
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
