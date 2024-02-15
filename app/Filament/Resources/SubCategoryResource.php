<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubCategoryResource\Pages;
use App\Filament\Resources\SubCategoryResource\RelationManagers;
use App\Filament\Resources\SubCategoryResource\RelationManagers\ItemsRelationManager;
use App\Models\Category;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SubCategoryResource extends Resource
{
    protected static ?string $model = SubCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-stack';
    protected static ?string $navigationGroup = 'Manage Items';

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_type=='1';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\Select::make('category_id')
                    ->label('Select Category')
                    ->options(Category::query()->pluck('category_name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('subcategory_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                ])->columnSpanFull()->compact()

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('*')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('subcategory_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable(),
                TextColumn::make('category.category_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->sortable(),
                TextColumn::make('description')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
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
            ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubCategories::route('/'),
            'create' => Pages\CreateSubCategory::route('/create'),
            'edit' => Pages\EditSubCategory::route('/{record}/edit'),
        ];
    }
}
