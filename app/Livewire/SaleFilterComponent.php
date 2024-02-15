<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Branch;
use App\Models\SubCategory;
use App\Models\Item;
use App\Models\BranchStock;
use App\Models\Sale;

class SaleFilterComponent extends Component
{
    public $subCategoryId;
    public $itemId;
    public $branchId;

    protected $listeners = ['updateItems'];

    public function mount()
    {
        $this->subCategoryId = null;
        $this->itemId = null;
        $this->branchId = null;
    }

    public function render()
    {
        $subCategories = SubCategory::all();
        $items = Item::when($this->subCategoryId, function ($query) {
            $query->where('sub_category_id', $this->subCategoryId);
        })->get();

        $sales = Sale::when($this->itemId, function ($query) {
            $query->whereHas('branchStock.mainStock.item', function ($itemQuery) {
                $itemQuery->where('id', $this->itemId);
            });
        })->get();

        return view('livewire.sale-filter-component', [
            'subCategories' => $subCategories,
            'items' => $items,
            'sales' => $sales,
        ]);
    }

    public function updateItems()
    {
        $this->itemId = null;
    }

    public function applyFilters()
    {
        // Perform any additional logic when the form is submitted
        // You can fetch data or perform other actions here
        // For example, you can emit an event to notify a parent component
        // $this->emit('filtersApplied');
    }
}

