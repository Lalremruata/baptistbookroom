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
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Tables\Filters\SelectFilter;

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
                    ->afterStateUpdated(function(callable $set, Get $get){
                        $barcode = $get('barcode');
                        $items = Item::where('barcode', $barcode)->get();
                        if($items->count() === 1) {
                            $set('item_id', $items->first()->id);
                        } elseif ($items->count() > 1) {
                            // Clear item_id to force user selection if multiple items found
                            $set('item_id', null);
                        }
                    })
                    ->autofocus()
                    ->live()
                    ->dehydrated(),

                Forms\Components\Select::make('sub_category_id')
                    ->label('Sub Category')
                    ->options(SubCategory::query()->pluck('subcategory_name', 'id'))
                    ->afterStateUpdated(fn(callable $set) => $set('item_id', null))
                    ->reactive()
                    ->searchable(),

                Forms\Components\Select::make('item_id')
                    ->label('Item')
                    ->options(function (callable $get) {
                        $subCategory = SubCategory::find($get('sub_category_id'));
                        $barcode = $get('barcode');
                        $items = Item::where('barcode', $barcode)->get();

                        if ($barcode && $items->isNotEmpty()) {
                            return $items->pluck('item_name', 'id');
                        } elseif ($subCategory) {
                            return $subCategory->items->pluck('item_name', 'id');
                        }

                        return Item::query()->pluck('item_name', 'id');
                    })
                    ->afterStateUpdated(function(callable $set, Get $get) {
                        // Populate the barcode field when item is selected
                        $selectedItem = Item::find($get('item_id'));
                        if($selectedItem) {
                            $set('barcode', $selectedItem->barcode);
                        }
                    })
                    ->reactive()
                    ->searchable()
                    ->required(),
            ])
            ->compact()
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
                    ->summarize(Sum::make()->label('Total'))
                    ->rules(['required', 'numeric'])
                    ->sortable(),
//                    ->afterStateUpdated(function ($record, $state) {
//                        $privateBook = PrivateBook::where('main_stock_id', $record['id'])->first();
//                        if ($privateBook) {
//                            // Update the quantity column
//                            $privateBook->update(['quantity' => $state]);
//                        }
//                    }),
                TextInputColumn::make('cost_price')
                    ->summarize(Sum::make()->label('Total'))
                    ->rules(['required', 'numeric'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $branchStocks = BranchStock::where('main_stock_id',$record['id'])->get();
                        foreach ($branchStocks as $branchStock) {
                            $branchStock->update(['cost_price' => $state]);
                        }
                    }),
                TextInputColumn::make('mrp')
                    ->summarize(Sum::make()->label('Total'))
                    ->rules(['required', 'numeric'])
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $branchStocks = BranchStock::where('main_stock_id', $record['id'])->get();
                        foreach ($branchStocks as $branchStock) {
                            $branchStock->update(['mrp' => $state]);
                        }
                    }),
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable()
                    // ->afterStateUpdated(function ($record, $state) {
                    //     $branchStock = BranchStock::where('main_stock_id', $record['id']);
                    //     $item = Item::where('id', $record['item_id'])->first();
                    //     if ($branchStock) {
                    //         // Update the quantity column
                    //         $branchStock->update(['barcode' => $state]);
                    //         $item->update(['barcode' => $state]);
                    //     }
                    // })
                    ,
                TextInputColumn::make('item.gst_rate')
                    ->label('GST Rate')
                    ->searchable()
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $item = Item::find($record['item_id']);
                        if ($item) {
                            $item->update(['gst_rate' => $state]);
                        }
                    }),
                TextInputColumn::make('item.hsn_number')
                    ->label('HSN Number')
                    ->searchable()
                    ->sortable()
                    ->afterStateUpdated(function ($record, $state) {
                        $item = Item::find($record['item_id']);
                        if ($item) {
                            $item->update(['hsn_number' => $state]);
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
                SelectFilter::make('category')
                ->relationship('item.category','category_name'),
                SelectFilter::make('subCategory')
                ->relationship('item.subCategory','subcategory_name'),

            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(4)
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
                    ->summarize(Sum::make()->label('Total'))
                    ->sortable(),
                TextColumn::make('mrp')
                    ->summarize(Sum::make()->label('Total'))
                    ->sortable(),
                TextColumn::make('barcode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.gst_rate')
                    ->label('GST Rate')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('item.hsn_number')
                    ->label('HSN Number')
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
                SelectFilter::make('category')
                ->relationship('item.category','category_name'),
                SelectFilter::make('subCategory')
                ->relationship('item.subCategory','subcategory_name'),

            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(4)
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            'view' => Pages\ViewMainStock::route('/{record}'),
        ];
    }
    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            // ...
            Pages\ViewMainStock::class,
        ]);
    }
}
