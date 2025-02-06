<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\Sale;
use App\Models\SubCategory;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;

class AllBranchStock extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Stocks';
    protected static string $view = 'filament.pages.all-branch-stock';
    protected static ?int $navigationSort = 5;

    public ?array $data = [];
    public $itemName = "";
    public $branches = [];
    public $sales = [];
    public $branchStock = [];
    public $mainStock = "";
    public $isBarcodeValid = false; // Flag to track barcode validity

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
                            ->afterStateUpdated(function (callable $set, Get $get) {
                                $barcode = $get('barcode');
                                $item = Item::where('barcode', $barcode)->first();

                                if ($item) {
                                    $set('item_id', $item->id);
                                    $set('sub_category_id', $item->sub_category_id);
                                    $this->isBarcodeValid = true; // Barcode is valid
                                } else {
                                    $this->isBarcodeValid = false; // Barcode is invalid
                                }

                                // Trigger AllBranchStock::showTable() after updating barcode
                                $this->showTable();
                            })
                            ->autofocus()
                            ->live()
                            ->reactive()
                            ->required()
                            ->dehydrated(),
                        Select::make('sub_category_id')
                            ->label('Sub Category')
                            ->searchable()
                            ->options(SubCategory::query()->pluck('subcategory_name', 'id'))
                            ->afterStateUpdated(fn (callable $set) => $set('item_id', null))
                            ->reactive()
                            ->required(),
                        Select::make('item_id')
                            ->label('Item')
                            ->options(function (callable $get) {
                                $subCategory = SubCategory::find($get('sub_category_id'));
                                $item = Item::find($get('barcode'));

                                if (!$subCategory && $get('barcode')) {
                                    return Item::query()->pluck('item_name', 'id');
                                } elseif (!$subCategory && !$item) {
                                    return null;
                                }

                                return $subCategory->items->pluck('item_name', 'id');
                            })
                            ->afterStateUpdated(function (callable $set, Get $get) {
                                $set('barcode', Item::query()
                                    ->where('id', $get('item_id'))
                                    ->pluck('barcode')
                                    ->first());
                                    $this->isBarcodeValid = true;
                                $this->showTable();
                            })
                            ->reactive()
                            ->searchable()
                            ->required(),
                    ])
                    ->columns(4)
                    ->compact(),
            ])
            ->statePath('data');
    }

    public function showTable(): void
    {
        $data = $this->form->getState();
        $itemId = $data['item_id'] ?? null;

        if ($itemId) {
            $itemName = Item::where('id', $itemId)->pluck('item_name')->first();
            $branches = Branch::all();
            $branchStock = BranchStock::with(['mainStock' => function ($query) {
                $query->select('id', 'quantity');
            }])
                ->whereHas('item', function ($query) use ($itemId) {
                    $query->where('items.id', $itemId);
                })
                ->get();

            $sales = Sale::with('branch:branch_name')
                ->whereHas('item', function ($query) use ($itemId) {
                    $query->where('items.id', $itemId);
                })
                ->get();

            $mainStock = MainStock::whereHas('item', function ($query) use ($itemId) {
                $query->where('items.id', $itemId);
            })
                ->pluck('quantity')
                ->first();

            $this->itemName = $itemName;
            $this->branches = $branches;
            $this->sales = $sales;
            $this->branchStock = $branchStock;
            $this->mainStock = $mainStock;
        } else {
            // Reset values if no item is found
            $this->itemName = "";
            $this->branches = [];
            $this->sales = [];
            $this->branchStock = [];
            $this->mainStock = "";
        }
    }
}