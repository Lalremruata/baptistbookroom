<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\MainStock;
use App\Models\StockDistribute;
use App\Models\StockDistributeCart;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\HasTabs;
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
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Filament\Actions\StaticAction;
use Filament\Resources\Components\Tab;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;


class StockDistributeCarts extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = StockDistributeCart::class;
    public StockDistributeCart $stockDistributeCart;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    use HasTabs;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'Main Stock Distribute';
    protected static ?int $navigationSort = 2;
    public $branches;
    public $selectedTab = 'all';
    protected static string $view = 'filament.pages.stock-distribute-cart';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_type;
    }
    public static function getEloquentQuery(): Builder
    {
        if(auth()->user()->user_type == '1') {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            return parent::getEloquentQuery()->where('branch_id', auth()->user()->branch_id);

        }
    }
    public function mount(): void
    {
        $this->form->fill();
        $this->branches = Branch::all();
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
                        $mainStock = MainStock::where('barcode', $barcode)
                        ->first();
                        if($mainStock)
                        {
                            $set('item_id', $mainStock->item_id);
                            $set('main_stock_id',$mainStock->id);
                        }

                    })
                    ->reactive()
                    ->live(),
                    Select::make('item_id')
                        ->reactive()
                        ->searchable()
                        ->label('Item')
                        ->options(MainStock::with('item')->get()->pluck('item_info', 'item_id')->toArray())
                        ->afterStateUpdated(
                            function(callable $set,Get $get){
                                $itemId = $get('item_id');
                                $mainStock = MainStock::where('item_id', $itemId)
                                    ->first();
                                    if($mainStock)
                                    {
                                        $set('barcode',$mainStock->barcode);
                                        $set('main_stock_id',$mainStock->id);
                                    }
                            }
                            )
                        ->required()
                        ->dehydrated(),
                    Select::make('branch_id')
                        ->label('Branch')
                        ->searchable()
                        ->required()
                        ->options(Branch::pluck('branch_name','id')->toArray()),
                    TextInput::make('quantity')
                    ->reactive()
                    ->required()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $main_stock_id = $get('main_stock_id');
                        if ($main_stock_id) {
                            $mainStockQuantity=MainStock::where('id',$main_stock_id)
                            ->pluck('quantity','id')->first();
                            $stockDistributeQuantity=StockDistributeCart::where('main_stock_id',$main_stock_id)
                            ->sum('quantity');
                            $result = $mainStockQuantity-$stockDistributeQuantity;
                             return $result;
                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $main_stock_id = $get('main_stock_id');
                        if ($main_stock_id) {
                            $mainStockQuantity=MainStock::where('id',$main_stock_id)
                            ->pluck('quantity','id')->first();
                            $stockDistributeQuantity=StockDistributeCart::where('main_stock_id',$main_stock_id)
                            ->where('user_id',auth()->user()->id)
                            ->sum('quantity');
                            $result = $mainStockQuantity-$stockDistributeQuantity;
                            if($result)
                            return 'qty. available: '.$result;
                        else
                            return 'stock unavailable';
                        }
                            return null;
                    })
                        ->hintColor('danger')
                    ->numeric()
                    ->hidden(function (Get $get): bool {
                        if(MainStock::where('barcode', $get('barcode'))->first() || $get('item_id'))
                            return 0;
                        else return 1;
                    }),
                    Hidden::make('user_id')
                    ->default(auth()->user()->id),
                    Hidden::make('main_stock_id'),

                ])->columns(2)

            ])
            ->statePath('data');
    }

    public function setActiveTab($tabName)
    {
        $this->selectedTab = $tabName;
    }

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                    ->badge(StockDistributeCart::where('user_id', auth()->user()->id)->count())
                    ->badgeColor('success'),
        ];

        // Get branches that have StockDistributeCart records for the logged-in user
        $branches = Branch::whereHas('stockDistributeCart', function ($query) {
            $query->where('user_id', auth()->user()->id); // Filter by logged-in user
        })
        ->withCount(['stockDistributeCart' => function ($query) {
            $query->where('user_id', auth()->user()->id); // Count for logged-in user
        }])
        ->distinct()
        ->get();
        foreach ($branches as $branch) {
            $branchName = $branch->branch_name;
            $tabs[$branchName] = Tab::make($branchName)
                ->badge($branch->stock_distribute_cart_count)
                ->badgeColor('primary')
                ->modifyQueryUsing(function ($query) use ($branch) {
                    return $query->where('branch_id', $branch->id)
                                 ->where('user_id', auth()->user()->id); // Filter by user_id and branch
                });
        }

        return $tabs;
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(function () {
            $query = StockDistributeCart::query()->where('user_id', auth()->user()->id);

            if ($this->selectedTab !== 'all') {
                $branch = Branch::where('branch_name', $this->selectedTab)->first();
                if ($branch) {
                    $query->where('branch_id', $branch->id);
                }
            }

            return $query;
        })
            ->columns([
                TextColumn::make('mainStock.item.item_name'),
                TextColumn::make('mainStock.barcode')
                ->label('barcode'),
                TextColumn::make('quantity'),
                TextColumn::make('cost_price')
                ->label('Cost Price'),
                TextColumn::make('branch.branch_name'),
                TextColumn::make('mrp')
                ->summarize(Summarizer::make()
                ->label('Total')
                ->using(function (Builder $query): string {
                    return $query->sum(DB::raw('mrp * quantity'));
                }),

            ),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
                    DeleteBulkAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('print receipt')
                ->form([
                    Select::make('branch_id')
                    ->options(Branch::query()->pluck('branch_name','id'))
                        ->autofocus()
                        ->required(),
                ])
                ->action(function (array $data) {
                    return redirect()->route('stockdistribute.receipt.download', $data);
                })
                ->keyBindings(['command+p', 'shift+p'])
                ->icon('heroicon-o-printer')
                ->color('success'),
                \Filament\Tables\Actions\Action::make('checkout cart')
                ->form([
                    Select::make('branch_id')
                        ->label('Branch')
                        ->options(Branch::query()->pluck('branch_name', 'id'))
                        ->searchable()
                        ->required(),
                        // ->default($this->selectedTab),
                ])
                ->label('checkout cart')
                ->color('warning')
                ->icon('heroicon-o-bolt')
                ->extraAttributes([
                    'class' => 'flex justify-start',
                ])

                ->requiresConfirmation()
                ->action(function (array $data) {
                    $maxRetries = 5;
                    $retryCount = 0;
                    $delay = 100; // Initial delay for retries
                
                    while ($retryCount < $maxRetries) {
                        try {
                            DB::transaction(function () use ($data) {
                                // Retrieve all items from the user's stock distribute cart
                                $cartItems = StockDistributeCart::where('user_id', auth()->user()->id)
                                    ->where('branch_id', $data['branch_id'])
                                    ->get();
                
                                foreach ($cartItems as $item) {
                                    // Lock the main stock row to avoid concurrent updates
                                    $mainstock = MainStock::where('id', $item->main_stock_id)
                                        ->lockForUpdate()
                                        ->first();
                
                                    // Ensure the main stock exists before processing
                                    if ($mainstock) {
                                        // Deduct the quantity from main stock
                                        if ($mainstock->quantity >= $item->quantity) {
                                            $mainstock->quantity -= $item->quantity;
                                            $mainstock->save();
                                        } else {
                                            throw new \Exception('Insufficient main stock quantity.');
                                        }
                
                                        // Update or create branch stock with lock to prevent concurrent updates
                                        $branchstock = BranchStock::where('branch_id', $data['branch_id'])
                                            ->where('main_stock_id', $item->main_stock_id)
                                            ->lockForUpdate()
                                            ->first();
                
                                        if ($branchstock) {
                                            // Update the existing branch stock quantity
                                            $branchstock->quantity += $item->quantity;
                                            $branchstock->save();
                                        } else {
                                            // Create new branch stock if it doesn't exist
                                            BranchStock::create([
                                                'main_stock_id' => $item->main_stock_id,
                                                'quantity' => $item->quantity,
                                                'cost_price' => $mainstock->cost_price,
                                                'barcode' => $mainstock->barcode,
                                                'branch_id' => $data['branch_id'],
                                                'batch' => $mainstock->batch,
                                                'mrp' => $mainstock->mrp,
                                            ]);
                                        }
                
                                        // Create a stock distribute record
                                        StockDistribute::create([
                                            'main_stock_id' => $item->main_stock_id,
                                            'quantity' => $item->quantity,
                                            'cost_price' => $item->cost_price,
                                            'mrp' => $item->mrp,
                                            'batch' => $item->batch,
                                            'branch_id' => $data['branch_id'],
                                        ]);
                
                                        // Delete the cart item after distribution is done
                                        $item->delete();
                                    }
                                }
                            });
                
                            // Success Notification
                            Notification::make()
                                ->success()
                                ->title('Items distributed successfully!')
                                ->color('success')
                                ->send();
                
                            break; // Exit loop if successful
                
                        } catch (\Illuminate\Database\QueryException $e) {
                            if ($e->getCode() === '40001') { // Deadlock error code
                                $retryCount++;
                                Log::warning("Deadlock encountered. Retry attempt {$retryCount} of {$maxRetries}");
                
                                if ($retryCount >= $maxRetries) {
                                    throw $e; // After max retries, propagate the error
                                }
                
                                usleep($delay * 1000); // Wait before retrying
                                $delay *= 2; // Exponential backoff
                
                            } else {
                                // Handle other database exceptions
                                throw $e;
                            }
                
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            Log::error('Stock distribution failed: ' . $e->getMessage());
                
                            // Failure Notification
                            Notification::make()
                                ->danger()
                                ->title('Failed to distribute items!')
                                ->body('An error occurred during the stock distribution process.')
                                ->color('danger')
                                ->send();
                
                            break; // Break out of retry loop for non-deadlock exceptions
                        }
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
                ->submit('save')
                ->color('warning')
                ->icon('heroicon-o-shopping-cart'),
        ];
    }
    public function save(): void
    {
        try {
            DB::transaction(function() {
                // Retrieve form state (the data submitted via the form)
                $data = $this->form->getState();

    
                // Check if the item is already in the cart
                $cartItem = StockDistributeCart::where('main_stock_id', $data['main_stock_id'])
                ->where('user_id',auth()->user()->id)
                ->where('branch_id',$data['branch_id'])
                ->first();
                
                if ($cartItem) {
                    // If the item exists in the cart, increment the quantity
                    $cartItem->quantity += $data['quantity'];
                    $cartItem->update();
                } else {
                    // Otherwise, create a new cart item with additional data from MainStock
                    $mainStock = MainStock::where('id', $data['main_stock_id'])->first();
                    $newData = [
                        'cost_price' => $mainStock->cost_price,
                        'mrp' => $mainStock->mrp,
                        'batch' => $mainStock->batch,
                    ];
                    // Merge the new data with the form data
                    $data += $newData;
                    // Create a new cart entry
                    StockDistributeCart::create($data);
                }
            });
    
            // Success notification
            Notification::make()
                ->success()
                ->title('Item added')
                ->body('The item has been added to cart successfully.')
                ->color('success')
                ->send();
    
            // Clear the form after submission
            $this->form->fill();
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Stock distribution failed: ' . $e->getMessage());
    
            // Failure notification
            Notification::make()
                ->danger()
                ->title('Failed to add items!')
                ->body('An error occurred during the process.')
                ->color('danger')
                ->send();
        }
    }
    
    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn () => $this->cartItem->delete());
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
