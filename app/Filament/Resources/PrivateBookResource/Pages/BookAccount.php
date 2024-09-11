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
class BookAccount extends Page implements HasForms,  HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
     protected static string $resource = PrivateBookResource::class;
    
     public PrivateBook $record;
     public $totalQuantity;
     public $totalSale;
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
        $mainStock = MainStock::where('id', $this->record->id)->first(['quantity']);
        $branchStockQuantity = BranchStock::where('main_stock_id', $this->record->id)->sum('quantity');

        // Total sale quantity based on item_id
        $this->totalSale = Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })->sum('quantity');

        // Total returns for the book
        $totalReturns = PrivateBookReturn::where('private_book_id', $this->record->id)->sum('return_amount');

        // Calculate total quantity using null coalescing to handle potential null values
        $this->totalQuantity = ($mainStock->quantity ?? 0)
            + $branchStockQuantity
            + $this->totalSale
            + $totalReturns;


    }
    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->query(PrivateBookAccount::query()->where('private_book_id', $this->record->id))
    //         ->columns([
    //             TextColumn::make('return_amount')
    //             ->label('Payment Amount')
    //             ->width('5%')
    //             ->numeric(),
    //             TextColumn::make('date')
    //             ->label('date')
    //             ->date(),
    //         ])
    //         ->headerActions([
    //             \Filament\Tables\Actions\CreateAction::make('add record')
    //             ->form([
    //                 Section::make([
    //                     TextInput::make('return_amount')
    //                     ->label('Payment amount')
    //                     ->required(),
    //                     DatePicker::make('return_date')
    //                     ->label('Payment date')
    //                     ->default(now())
    //                 ])->columns(2)
    //                 ])

    //             ->label('Payment')
    //             ->color('success')
    //             ->extraAttributes([
    //                 'class' => 'margin',
    //             ])
    //             ->action(function (array $data, $record) {
    //                 $privateBookAccount = new PrivateBookAccount();
    //                 $privateBookAccount->private_book_id = $this->record->id;
    //                 $privateBookAccount->return_amount = $data['return_amount'];
    //                 $privateBookAccount->return_date = $data['return_date'];
    //                 $privateBookAccount->save();
    //             })
    //         ]);
    // }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }
}
