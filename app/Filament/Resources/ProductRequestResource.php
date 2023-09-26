<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductRequestResource\Pages;
use App\Filament\Resources\ProductRequestResource\RelationManagers;
use App\Models\ProductRequest;
use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductRequestResource extends Resource
{
    protected static ?string $model = ProductRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quantity_requested')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('request_date')
                    ->required(),
                Radio::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                Select::make('product_id')
                    ->relationship('product','product_name'),
                Select::make('branch_id')
                    ->relationship('branch','branch_name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.product_name')
                ->sortable(),
                TextColumn::make('branch.branch_name')
                ->sortable(),
                SelectColumn::make('status')
                ->options([
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                ]),
                TextColumn::make('quantity_requested')
                ->sortable(),
                TextColumn::make('request_date')
                ->sortable(),
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
            'index' => Pages\ListProductRequests::route('/'),
            'create' => Pages\CreateProductRequest::route('/create'),
            'edit' => Pages\EditProductRequest::route('/{record}/edit'),
        ];
    }
}
