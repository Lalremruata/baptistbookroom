<?php

namespace App\Filament\Pages;

use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\Sale;
use Filament\Actions\Action;
use App\Models\SalesCartItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\VerticalAlignment;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SalesCart extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = SalesCartItem::class;
    public SalesCartItem $salesCartItem;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Sales cart';
    protected static ?string $navigationGroup = 'Sales';

    protected static string $view = 'filament.pages.sales-cart';
    public function mount(): void
    {
        $this->form->fill();
    }
    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->roles->contains('title', 'Admin')) {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            return parent::getEloquentQuery()->where('branch_id', auth()->user()->branch_id);

        }
    }
    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema([
                TextInput::make('barcode')
                ->label('Barcode Search')
                ->autofocus()
                ->afterStateUpdated(function(callable $set,Get $get){
                    $barcode = $get('barcode');
                    if($barcode)
                    {
                        $branchStock = BranchStock::with('mainStock')
                        ->where('barcode', $barcode)
                        ->first();
                        if($branchStock)
                        {
                            $set('branch_stock_id', $branchStock->id);
                        }
                    }

                })
                ->reactive()
                ->live(),
            Select::make('branch_stock_id')
                ->reactive()
                ->label('Item Search')
                ->options(function(){
                     return BranchStock::with(['mainStock' => function ($query) {
                        $query->select('item_id', 'id');
                    }])
                    ->whereHas('mainStock', function ($query) {
                    $query->where('branch_id', auth()->user()->branch_id);
                    })
                    ->get()
                    ->pluck('mainStock.item.item_name', 'id')
                    ->toArray();
                })
                ->searchable()
                ->dehydrated()
                ->required()
                ->live(),
                TextInput::make('quantity')
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $branchStockId = $get('branch_stock_id');
                        if ($branchStockId) {
                            $result=BranchStock::where('id',$branchStockId)
                            ->where('branch_id',auth()->user()->branch_id)
                            ->pluck('quantity','id')->first();
                                return $result;

                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $branchStockId = $get('branch_stock_id');
                        $barcode = $get('barcode');
                        if ($branchStockId) {
                                $result=BranchStock::where('id',$branchStockId)
                                ->where('branch_id',auth()->user()->branch_id)
                                ->pluck('quantity','id')->first();
                                if($result)
                                    return 'quantity available: '.$result;
                                else
                                    return 'stock unavailable';
                        }
                        elseif ($barcode) {
                            $result=BranchStock::where('barcode', $barcode)
                            ->where('branch_id',auth()->user()->branch_id)
                            ->pluck('quantity','id')->first();
                            if($result)
                                return 'quantity available: '.$result;
                           else
                               return 'stock unavailable';
                    }
                    // elseif()
                            return null;
                    })
                        ->hintColor('danger')
                        ->required()
                        ->hidden(function (Get $get): bool {
                            if(BranchStock::where('barcode', $get('barcode'))->first() || $get('branch_stock_id'))
                                return 0;
                            else return 1;
                        }),
                    TextInput::make('discount')
                    ->numeric()
                    ->default(0),
                Hidden::make('branch_id')
                    ->default(auth()->user()->branch_id),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                    ])->columns(2)

        ])->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(SalesCartItem::query()->where('branch_id', auth()->user()->branch_id))
            ->columns([
                TextColumn::make('branchStock.mainStock.item.item_name')
                    ->wrapHeader()
                    ->verticalAlignment(VerticalAlignment::Start),
                TextColumn::make('quantity')
                    ->verticallyAlignStart(),
                TextColumn::make('cost_price')
                ->suffix('/-'),
                TextColumn::make('selling_price')
                ->suffix('/-')
                // ->summarize(Summarizer::make()
                // ->label('Total')
                // ->using(function (Builder $query): string {
                //     return $query->sum(DB::raw('selling_price * quantity'));
                // }))
                ,
                TextColumn::make('discount')
                    ->suffix('%'),
            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
            // ...
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('checkout cart')

                ->steps([
                    Step::make('Customer Detail')
                    // ->description('Give the category a unique name')
                    ->schema([
                        Toggle::make('is_fully_paid')
                        ->label("Paid Full?")
                        ->default(1)
                        ->live(),
                        Section::make([
                            TextInput::make('customer_name')
                            ->label('customer name')
                            ->required()
                            // ->hidden(fn (Get $get): bool => ! $get('is_fully_paid'))
                            ,
                        TextInput::make('phone')
                            ->label('Contact')
                            // ->hidden(fn (Get $get): bool => ! $get('is_fully_paid'))
                            ,
                        ])->columns(2),
                    ]),
                    Step::make('Payment')
                    // ->description('Give the category a unique name')
                    ->schema([
                        Section::make([
                            TextInput::make('recieved_amount')
                            ->prefix('₹')
                            ->numeric()
                            ->required(),
                            Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer"=>"bank transfer",
                                "cheque" => "cheque"
                            ])
                            ->required(),
                            TextInput::make('Transaction_number')
                        ])->columns(2)
                        ])


                ])
                ->label('checkout cart')
                ->color('warning')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $salesData = [];
                    $cartItems = SalesCartItem::where('branch_id',auth()->user()->branch_id)
                    ->where('user_id',auth()->user()->id)->get();
                    foreach ($cartItems as $item) {
                        $branchStock = BranchStock::where('branch_id', $item->branch_id)
                        ->where('id', $item->branch_stock_id)
                        ->first();
                        $branchStock->quantity -= $item->quantity;
                        $branchStock->update();
                        $salesData[] = [
                            'branch_id' => auth()->user()->branch_id,
                            'user_id' => auth()->user()->id,
                            'branch_stock_id' => $item->branch_stock_id,
                            'sale_date' => now(),
                            'cost_price' => $branchStock['cost_price'],
                            'selling_price' => $branchStock['mrp'],
                            'quantity' =>$item->quantity,
                        ];
                        $item->delete();
                    }
                    Sale::insert($salesData);
                })
                ->slideOver()
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('danger')
                ->modalWidth(MaxWidth::TwoExtraLarge)
            ])
            ->paginated([25, 50, 100, 'all']);

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
            $cartItem = SalesCartItem::where('branch_stock_id', $data['branch_stock_id'])->first();
            if (!$cartItem) {
                $branchStock = BranchStock::where('id', $data['branch_stock_id'])->first();
                $totalCostPrice = $branchStock->cost_price * $data['quantity'];
                $sellingPrice = $totalCostPrice - ($totalCostPrice * ($data['discount']/100));
                $newData = [
                    'cost_price'=> $totalCostPrice,
                    'selling_price'=> $sellingPrice,
                ];
                $data += $newData;
                SalesCartItem::create($data);
            }
            else {
                $branchStock = BranchStock::where('id', $data['branch_stock_id'])->first();
                $totalCostPrice = $branchStock->cost_price * $data['quantity'];
                $sellingPrice = $totalCostPrice - ($totalCostPrice * ($data['discount']/100));
                $cartItem->quantity += $data['quantity'];
                $cartItem->cost_price += $totalCostPrice;
                $cartItem->selling_price += $sellingPrice;
                $cartItem->update();
            }
            $this->form->fill();
            // auth()->cartitem->save($data);
        } catch (Halt $exception) {
            return;
        }
    }

}
