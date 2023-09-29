<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockTransferResource\Pages;
use App\Filament\Resources\StockTransferResource\RelationManagers;
use App\Models\StockTransfer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Select Items')
                        ->schema([
                            Builder::make('content')
                            ->blocks([
                                Builder\Block::make('Add items')
                                    ->schema([
                                        Forms\Components\Select::make('item_id')
                                        ->relationship('item', 'item_name')
                                        ->required(),
                                        Forms\Components\TextInput::make('quantity')
                                        ->required()
                                        ->numeric(),
                                    Forms\Components\DatePicker::make('transfer_date')
                                        ->required(),
                                    Forms\Components\TextInput::make('notes')
                                        ->required()
                                        ->maxLength(255),
                                    ])
                        ])

                        ]),
                    Wizard\Step::make('Select Branch')
                        ->schema([
                            Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                                ->required(),
                        ]),
                    ])->submitAction(new HtmlString(Blade::render(<<<'BLADE'
                    <x-filament::button
                        wire:click="submit"
                    >
                        Submit
                    </x-filament::button>
                BLADE))),

            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('branch_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
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
            'index' => Pages\ListStockTransfers::route('/'),
            'create' => Pages\CreateStockTransfer::route('/create'),
            'edit' => Pages\EditStockTransfer::route('/{record}/edit'),
        ];
    }
}
