<?php

namespace App\Filament\Pages;

use App\Models\BranchStock;
use App\Models\CreditTransaction;
use App\Models\Customer;
use App\Models\Memo;
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
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
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
    public static function shouldRegisterNavigation(): bool
    {
        $allowedRoles = ['Admin', 'Agent'];
        return in_array(auth()->user()->roles->first()->title, $allowedRoles);
    }
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
                ->afterStateUpdated(function(callable $set, Get $get) {
                    $barcode = $get('barcode');
                    
                    // Fetch all branch stock records with the same barcode and belonging to the current branch
                    $branchStocks = BranchStock::with('mainStock')
                        ->where('barcode', $barcode)
                        ->where('branch_id', auth()->user()->branch_id)
                        ->get();
            
                    if ($branchStocks->count() === 1) {
                        // If only one item matches the barcode
                        $branchStock = $branchStocks->first();
                        $set('branch_stock_id', $branchStock->id);
                        $set('item_id', $branchStock->mainStock->item->item_name); // Assuming item is linked via MainStock
                    } elseif ($branchStocks->count() > 1) {
                        // If multiple items have the same barcode, clear and allow item selection
                        $set('branch_stock_id', null);
                        $set('item_id', null);
                    } else {
                        // If no match is found, clear the selection
                        $set('branch_stock_id', null);
                        $set('item_id', null);
                    }
                })
                ->reactive()
                ->live(),
            
            Select::make('item_id')
                ->reactive()
                ->label('Item Search')
                ->options(function (callable $get) {
                    $barcode = $get('barcode');
                    // Fetch all branch stock records with the same barcode and belonging to the current branch
                    $branchStocks = BranchStock::with('mainStock.item')
                        ->where('branch_id', auth()->user()->branch_id)
                        ->when($barcode, function($query) use ($barcode) {
                            return $query->where('barcode', $barcode);
                        })
                        ->get();
            
                    // Display items with item_info or other details
                    return $branchStocks->pluck('mainStock.item_info', 'id')->toArray();
                })
                ->afterStateUpdated(function (callable $set, Get $get) {
                    $branchStockId = $get('item_id');
                    $branchStock = BranchStock::with('mainStock')->find($branchStockId);
            
                     // Set GST rate and calculate GST amount
                     // If GST rate is not set, default to 0
                    $gstRate = $branchStock->mainStock->item->gst_rate ?? 0;
                    $mrp = $branchStock->mrp ?? 0;

                    $set('mrp', $mrp);
                    $set('gst_rate', $gstRate);

                    $price = $get('price');
                    $quantity = $get('quantity') ?? 1; // Default to 1 if quantity is not set
                    if ($price) {
                        $totalPrice = $price * $quantity;
                        $gstAmount = ($price * $gstRate) / 100;
                        $set('gst_amount', $gstAmount);
                    }
                    else {
                        $totalPrice = $mrp * $quantity;
                        $gstAmount = ($totalPrice * $gstRate) / 100;
                        $set('gst_amount', $gstAmount);
                    }
                    if ($branchStock) {
                        $set('barcode', $branchStock->barcode);
                        $set('branch_stock_id', $branchStock->id);
                    } else {
                        // Clear the barcode if no item is selected
                        $set('barcode', null);
                    }
                })
                ->noSearchResultsMessage('No items found.')
                ->searchingMessage('Searching items')
                ->searchable()
                ->dehydrated(false)
                ->required()
                ->live()
                ->hint(function(Get $get){
                    $branchStockId = $get('branch_stock_id');
                    $barcode = $get('barcode');
                    if ($branchStockId) {
                            $result=BranchStock::where('id',$branchStockId)
                            ->where('branch_id',auth()->user()->branch_id)
                            ->pluck('mrp','id')->first();
                            if($result)
                                return 'mrp: '.$result;
                            else
                                return null;
                    }
                    elseif ($barcode) {
                        $result=BranchStock::where('barcode', $barcode)
                        ->where('branch_id',auth()->user()->branch_id)
                        ->pluck('mrp','id')->first();
                        if($result)
                            return 'mrp: '.$result;
                       else
                           return null;
                }
                // elseif()
                        return null;
                })
                    ->hintColor('success'),
                TextInput::make('quantity')
                ->afterStateUpdated(function ($state, callable $set, Get $get) {
                    $price = $get('mrp');
                    $gstRate = $get('gst_rate');
                    $quantity = $state ?? 1; // Default to 1 if quantity is not set
                    if ($price && $gstRate) {
                        $totalPrice = $price * $quantity;
                        $gstAmount = ($totalPrice * $gstRate) / 100;
                        $set('gst_amount', $gstAmount);
                    }
                })
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $branchStockId = $get('branch_stock_id');
                        if ($branchStockId) {
                            $branchStockQuantity=BranchStock::where('id',$branchStockId)
                            ->where('branch_id',auth()->user()->branch_id)
                            ->pluck('quantity','id')->first();
                            $salesCartQuantity=SalesCartItem::where('branch_stock_id',$branchStockId)
                            ->where('branch_id', auth()->user()->branch_id)
                            ->where('user_id', auth()->user()->id)
                            ->pluck('quantity')->first();
                            $result = $branchStockQuantity-$salesCartQuantity;
                                return $result;

                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $branchStockId = $get('branch_stock_id');
                        $barcode = $get('barcode');
                        if ($branchStockId) {
                                $branchStockQuantity=BranchStock::where('id',$branchStockId)
                                ->where('branch_id',auth()->user()->branch_id)
                                ->pluck('quantity','id')->first();
                                $salesCartQuantity=SalesCartItem::where('branch_stock_id',$branchStockId)
                                ->where('branch_id', auth()->user()->branch_id)
                                ->where('user_id', auth()->user()->id)
                                ->pluck('quantity')->first();
                                $result = $branchStockQuantity-$salesCartQuantity;
                                if($result)
                                    return 'qty. available: '.$result;
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
                TextInput::make('mrp')
                    ->label('Price')
                    ->live()
                    ->numeric()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $price = $state;
                        $gstRate = $get('gst_rate');
                        $quantity = $get('quantity') ?? 1; // Default to 1 if quantity is not set
                        if ($price && $gstRate) {
                            $totalPrice = $price * $quantity;
                            $gstAmount = ($totalPrice * $gstRate) / 100;
                            $set('gst_amount', $gstAmount);
                        }
                    }),
                    TextInput::make('discount')
                    ->numeric()
                    ->default(0),
                TextInput::make('gst_amount')
                    ->label('GST Amount')
                    ->required()
                    ->reactive()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('gst_rate')
                    ->label('GST Rate')
                    ->required()
                    ->reactive()
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Hidden::make('branch_stock_id'),
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
                TextColumn::make('branchStock.item.item_name')
                    ->wrapHeader()
                    ->verticalAlignment(VerticalAlignment::Start),
                TextColumn::make('quantity')
                    ->verticallyAlignStart(),
                TextColumn::make('selling_price')
                ->suffix('/-')
                 ->summarize(Summarizer::make()
                 ->label('Total')
                 ->using(function (Builder $query): string {
                    return $query->sum('selling_price');
                    //  return $query->sum(DB::raw('selling_price * quantity'));
                 })),
                TextColumn::make('discount')
                    ->suffix('%'),
                TextColumn::make('gst_rate')
                    ->suffix('%'),
                TextColumn::make('gst_amount')
                    ->suffix('/-')
                    ->summarize(Summarizer::make()
                    ->label('Total')
                    ->using(function (Builder $query): string {
                        return $query->sum('gst_amount');
                    })),
                TextColumn::make('total_amount_with_gst')
                    ->suffix('/-')
                    ->summarize(Summarizer::make()
                    ->label('Total')
                    ->using(function (Builder $query): string {
                        return $query->sum('total_amount_with_gst');
                    })),

            ])
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
            // ...
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('print receipt')
                ->form([
                    TextInput::make('name')
                        ->autofocus()
                        ->required(),
                    TextInput::make('address'),
                    TextInput::make('gst_number')
                        ->label('GST Numbers')
                ])
                ->action(function (array $data) {
                    return redirect()->route('sale.receipt.download', $data);
                })
                // ->url(function(StockDistributeCart $stockDistributeCart){
                //     return route('stockdistribute.receipt.download', $stockDistributeCart);
                // })
                ->keyBindings(['command+p', 'shift+p'])
                ->icon('heroicon-o-printer')
                ->color('success'),
                \Filament\Tables\Actions\Action::make('checkout cart')
                ->steps([
                    Step::make('Payment')
                    // ->description('Give the category a unique name')

                    ->schema([
                        Section::make([
                            TextInput::make('received_amount')
                            ->prefix('₹')
                            ->numeric()
                            ->required()
                            ->hint(function(Get $get){
                                $totalAmount = SalesCartItem::where('branch_id', auth()->user()->branch_id)->sum('selling_price');
                                return 'Amount : ' . $totalAmount;
                            })
                            ->default(function(){
                                return SalesCartItem::where('user_id', auth()->user()->id)->sum('selling_price');
                            })
                            ->hintColor('danger'),
                            Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer"=>"bank transfer",
                                "cheque" => "cheque"
                            ])
                            ->required(),
                            TextInput::make('transaction_number')
                        ])->columns(2)
                            ]),

                    Step::make('Customer Detail')
                    ->afterValidation(function (array $data) {
                        // $customer = new Customer;
                        // dd($data);
                    })
                    ->schema([
                        Section::make([
                            TextInput::make('customer_name')
                            ->autofocus()
                            ->label('customer name'),
                        TextInput::make('phone')
                            ->label('Contact')
                            ->numeric(),
                        TextInput::make('address')
                            ->label('address')
                        ])->columns(2),
                    ]),
                ])
                ->label('checkout cart')
                ->color('warning')
                ->icon('heroicon-o-bolt')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $maxRetries = 5;
                    $retryCount = 0;
                    $delay = 100; // Initial delay for retries
                
                    while ($retryCount < $maxRetries) {
                        try {
                            DB::transaction(function () use ($data) {
                                $totalAmount = SalesCartItem::where('branch_id', auth()->user()->branch_id)
                                    ->where('user_id', auth()->user()->id)
                                    ->sum('selling_price');
                
                                if ($data['customer_name'] && $data['received_amount'] < $totalAmount) {
                                    // Handle customer creation within the transaction
                                    $customer = Customer::create([
                                        'customer_name' => $data['customer_name'],
                                        'phone' => $data['phone'],
                                        'address' => $data['address'],
                                    ]);
                                    $customer_id = $customer->id;
                                } else {
                                    $customer_id = null;
                                }
                
                                if ($data['received_amount'] < $totalAmount) {
                                    CreditTransaction::create([
                                        'customer_id' => $customer_id,
                                        'received_amount' => $data['received_amount'],
                                        'total_amount' => $totalAmount,
                                        'recovered_amount' => 0,
                                    ]);
                                }
                
                                // Memo safe update
                                $memo = Memo::lockForUpdate()->latest()->first();
                                $newMemo = $memo->memo + 1;
                                $memo->memo = $newMemo;
                                $memo->update();
                
                                $cartItems = SalesCartItem::where('branch_id', auth()->user()->branch_id)
                                    ->where('user_id', auth()->user()->id)
                                    ->get();
                
                                foreach ($cartItems as $item) {
                                    // Lock stock row to prevent concurrent updates
                                    $branchStock = BranchStock::where('branch_id', $item->branch_id)
                                        ->where('id', $item->branch_stock_id)
                                        ->lockForUpdate()
                                        ->first();
                                    $branchStock->quantity -= $item->quantity;
                                    $branchStock->update();
                
                                    // Create sale entry
                                    Sale::create([
                                        'branch_stock_id' => $item->branch_stock_id,
                                        'branch_id' => auth()->user()->branch_id,
                                        'user_id' => auth()->user()->id,
                                        'customer_id' => $customer_id,
                                        'quantity' => $item->quantity,
                                        'discount' => $item->discount,
                                        'total_amount' => $item->selling_price,
                                        'gst_rate' => $item->gst_rate,
                                        'gst_amount' => $item->gst_amount,
                                        'total_amount_with_gst' => $item->total_amount_with_gst,
                                        'payment_mode' => $data['payment_mode'],
                                        'transaction_number' => $data['transaction_number'],
                                        'memo' => $newMemo . auth()->user()->branch_id,
                                    ]);
                
                                    // Delete cart item
                                    $item->delete();
                                }
                            });
                
                            // Success Notification
                            Notification::make()
                                ->success()
                                ->title('Items sold successfully!')
                                ->color('success')
                                ->send();
                
                            break; // Break out of the retry loop if transaction is successful
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() === '40001') {
                                $retryCount++;
                                Log::warning("Deadlock encountered. Retry attempt {$retryCount} of {$maxRetries}");
                                if ($retryCount >= $maxRetries) {
                                    throw $e; // After max retries, propagate the error
                                }
                                usleep($delay * 1000); // Backoff delay
                                $delay *= 2; // Exponential backoff
                            } else {
                                throw $e; // Handle other exceptions
                            }
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Sales checkout failed: ' . $e->getMessage());
                
                            // Failure Notification
                            Notification::make()
                                ->danger()
                                ->title('Failed to checkout cart!')
                                ->body('An error occurred during the checkout process.')
                                ->send();
                            break; // Break if it's a general exception
                        }
                    }
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
                ->submit('save')
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
    public function save(): void
    {
        try {
            DB::transaction(function() {
            $data = $this->form->getState();
            $cartItem = SalesCartItem::where('branch_stock_id', $data['branch_stock_id'])->first();
            if (!$cartItem) {
                $branchStock = BranchStock::where('id', $data['branch_stock_id'])->first();
                $mrp = $data['mrp'] ?? $branchStock->mrp;
                $totalMrp= $mrp * $data['quantity'];
                $totalCostPrice = $branchStock->cost_price * $data['quantity'];
                $sellingPrice = $totalMrp - ($totalMrp * ($data['discount']/100));
                $gstRate = $data['gst_rate'];
                $gstAmount = ($sellingPrice * $gstRate) / 100;
                $totalAmountWithGst = $sellingPrice + $data['gst_amount'];
                $newData = [
                    'cost_price'=> $totalCostPrice,
                    'selling_price'=> $sellingPrice,
                    'gst_amount'=> $gstAmount,
                    'total_amount_with_gst'=> $totalAmountWithGst
                ];
                $data += $newData;
                SalesCartItem::create($data);
            }
            else {
                $branchStock = BranchStock::where('id', $data['branch_stock_id'])->first();
                $totalCostPrice = $branchStock->cost_price * $data['quantity'];
                $mrp = $data['mrp'] ?? $branchStock->mrp;
                $totalMrp= $mrp * $data['quantity'];
                $sellingPrice = $totalMrp - ($totalMrp * ($data['discount']/100));
                $cartItem->quantity += $data['quantity'];
                $cartItem->cost_price += $totalCostPrice;
                $cartItem->selling_price += $sellingPrice;
                $cartItem->gst_amount += $data['gst_amount'];
                $totalAmountWithGst = $sellingPrice + $data['gst_amount'];
                $cartItem->total_amount_with_gst += $totalAmountWithGst;
                $cartItem->update();
            }
            $this->form->fill();
            // auth()->cartitem->save($data);
                    // Dispatch the browser event to focus the input
           
            // Success notification
            Notification::make()
                ->success()
                ->title('Item added')
                ->body('The item has been added to cart successfully.')
                ->color('success')
                ->send();
    
            // Clear the form after submission
            $this->form->fill();
        });

    }   catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Item added to cart failed: ' . $e->getMessage());

        // Failure notification
        Notification::make()
            ->danger()
            ->title('Failed to add items!')
            ->body('An error occurred during the process.')
            ->color('danger')
            ->send();
    }
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('help')
            ->modalContent(function (): View {
                $record = "privateBook";
                return view('filament.pages.help', [
                    'record' => $record,
                ]);
            }
            )
            ->icon('heroicon-m-question-mark-circle')
            // ->iconButton()
            ->slideOver()
            ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
            ->modalSubmitAction(false)
            ->modalWidth(MaxWidth::Medium)
            ->modalAlignment(Alignment::Center)
        ];
    }

}
