<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\CartItem;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\StockDistribute;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Card;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class CartItems extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = CartItem::class;
    public CartItem $cartItem;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static string $view = 'filament.pages.cart-items';
    public function mount(): void
    {
        $this->form->fill();
        // $this->form->fill(auth()->user()->cartitem->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                ->schema([
                    Select::make('item_id')
                    ->live()
                    ->label('Item')
                    ->options(Item::query()->pluck('item_name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('quantity')
                    ->live()
                    ->required()
                    ->numeric(),
                    Hidden::make('user_id')
                    ->default(auth()->user()->id)
                ])->columns(2)

            ])
            ->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(CartItem::query())
            ->columns([
                TextColumn::make('item_id'),
                TextColumn::make('quantity'),
                TextColumn::make('user_id'),
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('checkout cart')
                ->form([
                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(Branch::query()->pluck('branch_name', 'id'))
                        ->searchable()
                        ->required(),
                ])
                ->label('checkout cart')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'left',
                ])

                ->requiresConfirmation()
                ->action(function (array $data) {
                    $cartItems = CartItem::all();
                    foreach ($cartItems as $item) {
                        $mainstock = MainStock::find($item->item_id);
                        $mainstock->quantity -= $item->quantity;
                        $mainstock->update();

                        $branchstock = BranchStock::find($item->item_id);
                        if ($branchstock) {
                            $branchstock->quantity += $item->quantity;
                            $branchstock->update();
                        }
                        else{
                            $branchstock = new Branchstock();
                            $branchstock->item_id = $item->item_id;
                            $branchstock->quantity = $item->quantity;
                            $branchstock->cost_price = $mainstock->cost_price;
                            $branchstock->branch_id = $data['branch_id'];
                            $branchstock->discount = 50;
                            $branchstock->save();
                        }
                        $stockdistribute = new StockDistribute();
                        $stockdistribute->item_id = $item->item_id;
                        $stockdistribute->quantity = $item->quantity;
                        $stockdistribute->branch_id = $data['branch_id'];
                        $stockdistribute->save();

                        $item->delete();
                    }

                })
                ->modalIcon('heroicon-o-check-circle')
                ->modalDescription('Select Branch Name')
                ->modalIconColor('danger')
            ]);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('Add to cart'))
                ->submit('save'),
        ];
    }
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            // dd(auth()->user()->id);
            CartItem::create($data);
            // auth()->cartitem->save($data);
        } catch (Halt $exception) {
            return;
        }
    }
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->cartItem->delete());
    }
}
