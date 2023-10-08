<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockDistributeResource\Pages;
use App\Filament\Resources\StockTransferResource\RelationManagers;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\StockDistribute;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockDistributeResource extends Resource
{
    protected static ?string $model = StockDistribute::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Main Stocks';


    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Card::make()
                ->schema([
                    Forms\Components\Select::make('branch_id')
                    ->relationship('branch', 'branch_name')
                    ->required(),
                ])->columns(3),


                Card::make()
                ->schema([
                    Repeater::make('stockDistributeItem')
                    ->relationship()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                        ->live()
                        ->searchable()
                        ->options(Item::query()->pluck('item_name', 'id'))
                        // ->afterStateUpdated(fn(callable $set) => $set('quantity', null))
                        ->required(),
                        Forms\Components\TextInput::make('quantity')
                        ->live()
                        ->hint(function(Get $get){
                            $itemId = $get('item_id');
                            if ($itemId) {
                                $result=MainStock::where('item_id',$itemId)->select('quantity')->first();
                                $quantity = $result->quantity;
                                return 'quantity available: '.$quantity;
                            }
                            return null;
                        })
                        ->hintColor('primary')
                        ->required()
                        ->integer(),


                    ])->columns(2)
                ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stockDistributeItem.item.item_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stockDistributeItem.quantity')
                ->label('quantities')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transfer_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'create' => Pages\CreateStockDistribute::route('/create'),
            'edit' => Pages\EditStockDistribute::route('/{record}/edit'),
        ];
    }
}
