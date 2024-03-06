<?php

namespace App\Filament\Resources;

use Filament\Tables\Actions\ExportAction;
use App\Filament\Exports\MainStockExporter;
use App\Filament\Resources\MainStockResource\Pages;
use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\SubCategory;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;

class MainStockResource extends Resource
{
    protected static ?string $model = MainStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('barcode')
                        ->afterStateUpdated(function(callable $set,Get $get){
                            $barcode = $get('barcode');
                            $item = Item::where('barcode', $barcode)
                            ->first();
                            if($item)
                            {
                                $set('item_id', $item->id);
                            }

                        })
                        ->autofocus()
                        ->live()
                        ->required()
                        ->dehydrated(),
                    Forms\Components\Select::make('sub_category_id')
                        ->label('Sub Category')
                        ->options(SubCategory::query()->pluck('subcategory_name', 'id'))
                        ->afterStateUpdated(fn(callable $set)=>$set('item_id', null))
                        ->reactive()
                        ->searchable(),
                    Forms\Components\Select::make('item_id')
                        ->label('Item')
                            ->options(function(callable $get){
                                $subCategory= SubCategory::find($get('sub_category_id'));
                                $item= Item::find($get('barcode'));
                                if(!$subCategory && $get('barcode')){
                                    return (Item::query()->pluck('item_name', 'id'));
                                    // return null;
                                }
                                elseif(!$subCategory && !$item){
                                    // return (Item::query()->pluck('item_name', 'id'));
                                    return null;
                                }
                                return $subCategory->items->pluck('item_name','id');
                            })
                            ->afterStateUpdated(fn(callable $set,Get $get)=>$set('barcode',Item::query()
                                ->where('id', $get('item_id'))->pluck('barcode')->first()))
                            ->reactive()
                            ->searchable()
                            ->required(),
                    ])->compact()
                    ->columns(3),
                Section::make()
                ->schema([
                    Forms\Components\TextInput::make('quantity')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('cost_price')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('mrp')
                        ->required()
                        ->numeric(),
                    Forms\Components\TextInput::make('batch')
                        ->required()
                        ->numeric(),

                ])->compact()
                ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        if(auth()->user()->user_type=='1'){
            return $table
            ->columns([
                TextColumn::make('*')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('item.item_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.category.category_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.SubCategory.subcategory_name')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextInputColumn::make('quantity')
                    ->rules(['required', 'numeric'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $privateBook = PrivateBook::where('main_stock_id', $record['id'])->first();
                        if ($privateBook) {
                            // Update the quantity column
                            $privateBook->update(['quantity' => $state]);
                        }
                    }),
                TextInputColumn::make('cost_price')
                    ->rules(['required', 'numeric'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $branchStock = BranchStock::where('main_stock_id', $record['id']);
                        if ($branchStock) {
                            $branchStock->update(['cost_price' => $state]);
                        }
                    }),
                TextInputColumn::make('mrp')
                    ->rules(['required', 'numeric'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $branchStock = BranchStock::where('main_stock_id', $record['id']);
                        if ($branchStock) {
                            $branchStock->update(['mrp' => $state]);
                        }
                    }),
                TextInputColumn::make('barcode')
                    ->searchable()
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $branchStock = BranchStock::where('main_stock_id', $record['id']);
                        $item = Item::where('id', $record['item_id'])->first();
                        if ($branchStock) {
                            // Update the quantity column
                            $branchStock->update(['barcode' => $state]);
                            $item->update(['barcode' => $state]);
                        }
                    }),
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

            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                // ExportBulkAction::make()
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MainStockExporter::class)
                    ->formats([
                        ExportFormat::Xlsx,
                    ])
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
            ], position: HeaderActionsPosition::Bottom)
            // ->defaultGroup('item_id')
            // ->groupRecordsTriggerAction(
            //     fn (Action $action) => $action
            //         ->button()
            //         ->label('Group records'),
            // )
            ;;
        }
        else{
            return $table
            ->columns([
                TextColumn::make('')
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                TextColumn::make('item.item_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.category.category_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.SubCategory.subcategory_name')
                    ->weight(FontWeight::Bold)
                    ->size(TextColumn\TextColumnSize::Large)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('quantity')
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->sortable(),
                TextColumn::make('mrp')
                    ->sortable(),
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable(),
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

            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(3)
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
                // ExportBulkAction::make()
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(MainStockExporter::class)
            ], position: HeaderActionsPosition::Bottom)
            // ->defaultGroup('item_id')
            // ->groupRecordsTriggerAction(
            //     fn (Action $action) => $action
            //         ->button()
            //         ->label('Group records'),
            // )
            ;
        }

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
            'index' => Pages\ListMainStocks::route('/'),
            'create' => Pages\CreateMainStock::route('/create'),
            'edit' => Pages\EditMainStock::route('/{record}/edit'),
        ];
    }
}
