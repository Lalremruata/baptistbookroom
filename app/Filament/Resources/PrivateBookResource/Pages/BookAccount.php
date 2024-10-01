<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\BranchStock;
use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\PrivateBookAccount;
use App\Models\PrivateBookReturn;
use App\Models\Sale;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Page;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
class BookAccount extends Page implements HasForms,  HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
     protected static string $resource = PrivateBookResource::class;

     public PrivateBook $record;
     public $totalQuantity;
     public $totalSale;
     public $initialQuantity;
    public $totalReturns;
     public ?array $data = [];
    protected static string $view = 'filament.resources.private-book-resource.pages.book-account';
    public function mount(): void
    {
        $this->form->fill();
        $this->initialQuantity();
    }
    public function initialQuantity(){
        $itemId = $this->record->item_id;

        // Fetch main stock, branch stock, and total sales in a single query
        $mainStock = MainStock::where('id', $this->record->main_stock_id)->first(['quantity']);
        $branchStockQuantity = BranchStock::where('main_stock_id', $this->record->main_stock_id)->sum('quantity');

        // Total sale quantity based on item_id
        $this->totalSale = Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->sum('quantity');

        // Total returns for the book
        $this->totalReturns = PrivateBookReturn::where('private_book_id', $this->record->id)->sum('return_amount');

        // Calculate total quantity using null coalescing to handle potential null values
        // dd($this->totalSale);
        $this->initialQuantity = ($mainStock->quantity ?? 0)
            + $branchStockQuantity
            + $this->totalSale
            + $this->totalReturns;
        $this->totalQuantity = ($mainStock->quantity ?? 0)
            + $branchStockQuantity;


    }
    public function refreshQuantities()
    {
        // Recalculate the quantities and balance
        $this->initialQuantity();

        // Optionally, you can calculate the balance here if it depends on the new data.
    }
    protected function getListeners()
    {
        return [
            'paymentUpdated' => 'refreshQuantities',
            'returnUpdated' => 'refreshQuantities',
        ];
    }
    public function getTitle(): string | Htmlable
    {
        /** @var PrivateBook */
        $record = $this->record;

        return $record->item->item_name;
    }

    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }
}
