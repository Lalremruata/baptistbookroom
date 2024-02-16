<?php

namespace App\Filament\Pages;

use App\Models\Category;
use App\Models\Item;
use App\Models\Sale;
use App\Models\SubCategory;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class AllBranchStocks extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Stocks';
    protected static string $view = 'filament.pages.all-branch-stocks';
    public ?array $data = [];
    public $sales = [];

    public function mount(): void
    {
        $this->form->fill();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    TextInput::make('barcode')
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
                Select::make('category_id')
                    ->label('Category')
                    ->searchable()
                    ->options(Category::query()->pluck('category_name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(fn(callable $set)=>$set('sub_category_id', null))
                    ->required(),
                Select::make('sub_category_id')
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
                    Select::make('item_id')
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
                ])
                ->columns(4)
                ->compact()
                ->collapsible(),

            ])
            ->statePath('data');
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('showTable')
                ->label(__('Apply Filter'))
                ->submit('showTable'),
        ];
    }

    public function showTable(): void
    {
        $data = $this->form->getState();
        $sales = Sale::when($data['item_id'], function ($query) use ($data) {
            $query->whereHas('branchStock.mainStock.item', function ($itemQuery) use ($data) {
                $itemQuery->where('id', $data['item_id']);
            });
        })->get();
        $this->sales = $sales;
        // $columns = [
        //     TextColumn::make('item_name')
        //         ->label('Item Name')
        //         ->sortable(),
        //     TextColumn::make('quantity')
        //         ->label('Quantity')
        //         ->sortable(),
        // ];
        // return $table
        // ->columns($columns)
        // ->rows(function (callable $get) use ($data) {
        //     // Query your data based on filters
        //     // ... (update filter conditions based on $data)
        //     $items = Sale::when($data['item_id'], function ($query) {
        //         $query->whereHas('branchStock.mainStock.item', function ($itemQuery) {
        //             $itemQuery->where('id', $this->itemId);
        //         });
        //     })->get();

        //     return $items->map(function ($item) use ($get) {
        //         return [
        //             'item_name' => $item->item_name,
        //             'quantity' => $item->stock->quantity, // Assuming you have a "stock" relationship
        //             'is_available' => $item->stock->is_available,
        //         ];
        //     });
        // });
    }

}
