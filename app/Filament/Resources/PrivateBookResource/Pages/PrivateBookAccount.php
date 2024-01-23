<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Table;

class PrivateBookAccount extends Page implements HasForms, HasTable, HasActions
{
    protected static string $resource = PrivateBookResource::class;
    public PrivateBookAccount $privateBookAccount;
    public ?array $data = [];
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    

    protected static string $view = 'filament.resources.private-book-resource.pages.private-book-account';
    public function mount(int | string $record): void
    {

        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
    }
    public function resolveRecord($record): ?PrivateBookAccount
    {
        return PrivateBookAccount::query()->where('private_book_id', $record)->first();

    }
    public function table(Table $table): Table
    {
        dd($this->record);
        return $table
            ->query(PrivateBookAccount::query()->where('private_book_id', $this->record->id))
            ->columns([
                // TextColumn::make('branchStock.mainStock.item.item_name'),
                // TextColumn::make('quantity'),
                // TextColumn::make('cost_price'),
                // TextColumn::make('selling_price'),
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
            // ...
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('checkout cart')
                ->label('checkout cart')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    // $salesData = [];
                    // $cartItems = SalesCartItem::where('branch_id',auth()->user()->branch_id)
                    // ->where('user_id',auth()->user()->id)->get();
                    // foreach ($cartItems as $item) {
                    //     $branchStock = BranchStock::where('branch_id', $item->branch_id)
                    //     ->where('id', $item->branch_stock_id)
                    //     ->first();
                    //     $branchStock->quantity -= $item->quantity;
                    //     $branchStock->update();
                    //     $salesData[] = [
                    //         'branch_id' => auth()->user()->branch_id,
                    //         'user_id' => auth()->user()->id,
                    //         'branch_stock_id' => $item->branch_stock_id,
                    //         'sale_date' => now(),
                    //         'cost_price' => $branchStock['cost_price'],
                    //         'selling_price' => $branchStock['mrp'],
                    //         'quantity' =>$item->quantity,
                    //     ];
                    //     $item->delete();
                    // }
                    // Sale::insert($salesData);
                })
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('danger')
            ])
            ->paginated([25, 50, 100, 'all']);

    }
}
