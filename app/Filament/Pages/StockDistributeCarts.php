<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\StockDistribute;
use App\Models\StockDistributeCart;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Get;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class StockDistributeCarts extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = StockDistributeCart::class;
    public StockDistributeCart $stockDistributeCart;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Distribution cart';
    protected static string $view = 'filament.pages.stock-distribute-cart';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_type;
    }
    public function mount(): void
    {
        $this->form->fill();
        // $this->form->fill(auth()->user()->cartitem->attributesToArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                ->schema([
                    Select::make('item_id')
                    ->reactive()
                    ->label('Item')
                    ->options(Item::query()->pluck('item_name', 'id'))
                        ->searchable()
                        ->required(),
                    TextInput::make('quantity')
                    ->reactive()
                    ->required()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $itemId = $get('item_id');
                        if ($itemId) {
                            $result=MainStock::where('item_id',$itemId)
                            ->pluck('quantity','id')->first();
                             return $result;
                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $itemId = $get('item_id');
                        if ($itemId) {
                            $result=MainStock::where('item_id',$itemId)

                            ->pluck('quantity','id')->first();
                            return 'qty available: '.$result;
                        }
                            return null;
                    })
                        ->hintColor('danger')
                    ->numeric(),
                    Hidden::make('user_id')
                    ->default(auth()->user()->id),

                ])->columns(2)

            ])
            ->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(StockDistributeCart::query())
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
                    'class' => 'flex justify-start',
                ])

                ->requiresConfirmation()
                ->action(function (array $data) {
                    $cartItems = StockDistributeCart::all();
                    foreach ($cartItems as $item) {
                        $mainstock = MainStock::where('item_id', $item->item_id)->first();
                        $mainstock->quantity -= $item->quantity;
                        $mainstock->update();

                        $branchstock = BranchStock::where('branch_id', $item->branch_id)
                        ->where('item_id', $item->item_id)
                        ->first();
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
            StockDistributeCart::create($data);
            $this->form->fill();
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
