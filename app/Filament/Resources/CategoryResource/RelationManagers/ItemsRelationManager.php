<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('sub_category_id')
                ->label('Sub Category')
                ->options(function (RelationManager $livewire): array {
                    return $livewire->getOwnerRecord()->subcategories()
                        ->pluck('subcategory_name', 'id')
                        ->toArray();
                }),
                Forms\Components\TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                    Forms\Components\TextInput::make('barcode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                Forms\Components\TextInput::make('gst_rate')
                    ->label('GST Rate')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('hsn_number')
                    ->label('HSN Number')
                    ->required()
                    ->numeric(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item_name')
            ->columns([
                Tables\Columns\TextColumn::make('item_name'),
                Tables\Columns\TextColumn::make('barcode'),
                Tables\Columns\TextColumn::make('description'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
