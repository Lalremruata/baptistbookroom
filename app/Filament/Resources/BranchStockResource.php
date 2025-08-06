<?php

namespace App\Filament\Resources;

use App\Filament\Exports\BranchStockExporter;
use App\Filament\Resources\BranchStockResource\Pages;
use App\Models\BranchStock;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder As QueryBuilder;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class BranchStockResource extends Resource
{
    protected static ?string $model = BranchStock::class;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';
    // protected static ?string $navigationParentItem = 'Distribute Report';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'Branch Stock Report';
    protected static ?int $navigationSort = 4;
    public static function canCreate(): bool
    {
        return 0;
    }

    public static function getEloquentQuery(): Builder
    {
        $allowedRoles = ['Admin', 'Manager'];
        // return in_array(auth()->user()->roles->first()->title, $allowedRoles);
        if(in_array(auth()->user()->roles->first()->title, $allowedRoles)) {
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
                // Forms\Components\TextInput::make('discount')
                //     ->required()
                //     ->numeric(),
                    ])->compact()
                    ->columns(2)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('*')
                ->weight(FontWeight::Bold)
                ->rowIndex(),
                TextColumn::make('branch.branch_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('mainStock.item.item_name')
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                TextColumn::make('quantity')
                    ->summarize(Sum::make()->label('Total'))
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                TextColumn::make('cost_price')
                    ->summarize(Summarizer::make()
                        ->numeric()
                    ->label('Total')
                    ->using(fn(QueryBuilder $query): float => $query->get()->sum(fn($row) => $row->cost_price * $row->quantity))
                    )
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mrp')
                    ->summarize(Summarizer::make()
                        ->numeric()
                    ->label('Total')
                    ->using(fn(QueryBuilder $query): float => $query->get()->sum(fn($row) => $row->mrp * $row->quantity))
                    )
                    ->weight(FontWeight::Bold)
                    ->numeric()
                    ->sortable(),
                TextColumn::make('mainStock.barcode')
                    ->label('Bar code')
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
            ])->searchDebounce('750ms')
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                        }),
                    // SelectFilter::make('branch')
                    //     ->relationship('branch','branch_name')
                    //     ->hidden(! auth()->user()->user_type=='1'),
                        SelectFilter::make('category_subcategory')
                        ->label('Category & Subcategory')
                        // Build the custom filter form
                        ->form([
                            // Build the Category dropdown
                            Forms\Components\Select::make('category_id')
                                ->label('Category')
                                // Select all Categories
                                ->options(
                                    fn() => \App\Models\Category::all()->pluck('category_name', 'id')->toArray()
                                )
                                // When category changes, check if currently selected subcategory belongs to the selected category
                                ->afterStateUpdated(function ($state, callable $get, callable $set) {
                                    $category = \App\Models\Category::find($state);

                                    if ($category) {
                                        $subcategoryId = (int) $get('subcategory_id');

                                        if ($subcategoryId && $subcategory = \App\Models\SubCategory::find($subcategoryId)) {
                                            if ($subcategory->category_id !== $category->id) {
                                                // Subcategory doesn't belong to category, so unselect it
                                                $set('subcategory_id', null);
                                            }
                                        }
                                    }
                                })
                                // Make the category dropdown reactive so we can rebuild the subcategory dropdown when state changes
                                ->reactive()
                                ->searchable()
                                ->placeholder('Select Category'),

                            // Build the SubCategory dropdown
                            Forms\Components\Select::make('subcategory_id')
                                ->label('Subcategory')
                                // Build the options based on selected category
                                ->options(function (callable $get) {
                                    $category = \App\Models\Category::find($get('category_id'));

                                    // If a category is selected, fetch subcategories for this category
                                    if ($category) {
                                        return $category->subCategories->pluck('subcategory_name', 'id');
                                    }

                                    // No category selected, so get all subcategories
                                    return \App\Models\SubCategory::all()->pluck('subcategory_name', 'id');
                                })
                                ->searchable()
                                ->placeholder(function (callable $get): string {
                                    return $get('category_id') ? 'Select Subcategory' : 'Select category';
                                })
//                                ->hint(function (callable $get): ?string {
//                                    $categoryId = $get('category_id');
//                                    if (!$categoryId) {
//                                        return null;
//                                    }
//
//                                    $count = \App\Models\SubCategory::where('category_id', $categoryId)->count();
//                                    return $count > 0 ? "{$count} subcategories available" : "No subcategories in this category";
//                                }),
                        ])
                        ->columns(2)
                        // Handle the query - this is where you define how this filter affects your main table
                        ->query(function (EloquentBuilder $query, array $data) {
                            // Get the category and subcategory IDs from the form's $data array
                            $categoryId = (int) $data['category_id'];
                            $subcategoryId = (int) $data['subcategory_id'];

                            // If a subcategory is selected, filter by subcategory (this implicitly includes the category)
                            if (!empty($subcategoryId)) {
                                $query->whereHas(
                                    'item',
                                    fn(EloquentBuilder $query) => $query->where('sub_category_id', '=', $subcategoryId)
                                );
                            }
                            // If only category is selected (no subcategory), filter by category
                            elseif (!empty($categoryId)) {
                                $query->whereHas(
                                    'item',
                                    fn(EloquentBuilder $query) => $query->where('category_id', '=', $categoryId)
                                );
                            }
                        }),
                ], layout: FiltersLayout::AboveContent)->filtersFormColumns(4)
            ->headerActions([
                ExportAction::make()
                    ->exporter(BranchStockExporter::class)
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
            'index' => Pages\ListBranchStocks::route('/'),
            'create' => Pages\CreateBranchStock::route('/create'),
            'view' => Pages\ViewBranchStock::route('/{record}'),
            'edit' => Pages\EditBranchStock::route('/{record}/edit'),
        ];
    }
}
