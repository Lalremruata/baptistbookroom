<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Http\Controllers\PrivateBookAccountReceiptController;
use App\Models\BranchStock;
use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\PrivateBookAccount;
use App\Models\PrivateBookReturn;
use App\Models\Sale;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Page;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Support\Enums\Alignment;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;

class BookAccount extends Page implements HasForms,  HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
     protected static string $resource = PrivateBookResource::class;

     public PrivateBook $record;
     public $totalQuantity;
     public $totalSale;
     public $initialQuantity;
     public $totalBookValue;
     public $balance;
    public $totalReturns;
    public $costPrice;
    public $mrp;

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
        $mainStock = MainStock::where('id', $this->record->main_stock_id)->first(['quantity','cost_price', 'mrp']);

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

        $this->totalBookValue = $this->totalQuantity * $mainStock->cost_price;
        $this->balance = $this->totalQuantity * $mainStock->cost_price;
        $this->costPrice = $mainStock->cost_price;
        $this->mrp = $mainStock->mrp;


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
        return MaxWidth::Full;
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('Print Receipt')
                ->icon('heroicon-o-printer')
                ->slideOver()
                ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
                ->modalSubmitAction(false)
                ->modalWidth(MaxWidth::Medium)
                ->modalAlignment(Alignment::Center)
                ->url(function () {
                    // Use $this->record to access the current PrivateBook
                    return route('private-book.receipt.download', ['privateBook' => $this->record->id]);
                }),
        ];
    }
}
