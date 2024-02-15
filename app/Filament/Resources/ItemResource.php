<?php

namespace App\Filament\Resources;

use App\Filament\Exports\ItemExporter;
use App\Filament\Resources\ItemResource\Pages;
use App\Models\Category;
use App\Models\Item;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Filament\Actions\Exports\Enums\ExportFormat;

class ItemResource extends Resource
{
    protected static ?string $model = Item::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup = 'Manage Items';
    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->roles->first()->title=='Admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->searchable()
                    ->options(Category::query()->pluck('category_name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set)=>$set('sub_category_id', null))
                    ->required(),
                Forms\Components\Select::make('sub_category_id')
                    ->label('Sub Categoty')
                    ->searchable()
                    ->options(function(callable $get){
                        $category= Category::find($get('category_id'));
                        if(!$category){
                            return null;
                        }
                        return $category->subcategories->pluck('subcategory_name','id');
                    })
                    ->reactive()
                    ->required(),
                ])->columns(2),
                Section::make()
                ->schema([
                Forms\Components\TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('barcode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
                    ->maxLength(255),
                ])->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('*')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('item_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable(),
                TextColumn::make('category.category_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->sortable(),
                TextColumn::make('subCategory.subcategory_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->sortable(),
                TextColumn::make('barcode')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->weight(FontWeight::Bold)
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
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ItemExporter::class)
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
            'index' => Pages\ListItems::route('/'),
            'create' => Pages\CreateItem::route('/create'),
            'edit' => Pages\EditItem::route('/{record}/edit'),
        ];
    }
}
