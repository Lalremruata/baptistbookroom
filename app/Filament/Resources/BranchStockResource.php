<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchStockResource\Pages;
use App\Filament\Resources\BranchStockResource\RelationManagers;
use App\Models\BranchStock;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchStockResource extends Resource
{
    protected static ?string $model = BranchStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Branch Stocks';
    protected static ?int $navigationSort = 2;
    public static function canCreate(): bool
    {
        return 0;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\Select::make('branch_id')
                    ->relationship('branch','branch_name')
                    ->required(),
                Forms\Components\Select::make('item_id')
                    ->searchable()
                    ->required()
                    ->relationship('items','item_name'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('cost_price')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('discount')
                    ->required()
                    ->numeric(),
                    ])->compact()
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.item_name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('cost_price')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('discount')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListBranchStocks::route('/'),
            'create' => Pages\CreateBranchStock::route('/create'),
            // 'edit' => Pages\EditBranchStock::route('/{record}/edit'),
        ];
    }
}
