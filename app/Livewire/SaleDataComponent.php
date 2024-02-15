<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;

class SaleDataComponent extends Component
{
    public $sales;
    public $selectedSubCategory;
    public $selectedItem;

    protected $listeners = ['subCategoryUpdated', 'itemUpdated'];

    public function render()
    {
        $this->sales = Sale::query()
            ->when($this->selectedSubCategory, function ($query) {
                $query->whereHas('branchStock.item.subCategory', function ($subQuery) {
                    $subQuery->where('id', $this->selectedSubCategory);
                });
            })
            ->when($this->selectedItem, function ($query) {
                $query->whereHas('branchStock.item', function ($subQuery) {
                    $subQuery->where('id', $this->selectedItem);
                });
            })
            ->get();

        return view('livewire.sale-data-component', ['sales' => $this->sales]);
    }

    public function subCategoryUpdated($value)
    {
        $this->selectedSubCategory = $value;
    }

    public function itemUpdated($value)
    {
        $this->selectedItem = $value;
    }
}

