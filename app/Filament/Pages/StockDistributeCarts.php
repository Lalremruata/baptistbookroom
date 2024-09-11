<?php

namespace App\Filament\Pages;

use App\Models\Branch;
use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\StockDistribute;
use App\Models\StockDistributeCart;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
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
use Filament\Forms\Components\Tabs;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\View\View;


class StockDistributeCarts extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = StockDistributeCart::class;
    public StockDistributeCart $stockDistributeCart;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'Stocks';
    protected static ?string $navigationLabel = 'Main Stock Distribute';
    protected static ?int $navigationSort = 2;
    public $branches;
    protected static string $view = 'filament.pages.stock-distribute-cart';
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()->user_type;
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
                            if($result)
                            return 'quantity available: '.$result;
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
    public function table(Table $table): Table
    {
        return $table
            ->query(StockDistributeCart::query()->where('user_id',auth()->user()->id))
            ->columns([
                TextColumn::make('mainStock.item.item_name'),
                TextColumn::make('mainStock.barcode')
                ->label('barcode'),
                TextColumn::make('quantity'),
                TextColumn::make('cost_price')
                ->label('Cost Price'),
                TextColumn::make('mrp')
                ->summarize(Summarizer::make()
                ->label('Total')
                ->using(function (Builder $query): string {
                    return $query->sum(DB::raw('mrp * quantity'));
                })
                )
            ])

            ->actions([
                DeleteAction::make()
            ])
            ->bulkActions([
                    DeleteBulkAction::make(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('print receipt')
                ->form([
                    Select::make('branch_name')
                    ->options(Branch::query()->pluck('branch_name','branch_name'))
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
                ])
                ->label('checkout cart')
                ->color('warning')
                ->icon('heroicon-o-bolt')
                ->extraAttributes([
                    'class' => 'flex justify-start',
                ])

                ->requiresConfirmation()
                ->action(function (array $data) {
                    $cartItems = StockDistributeCart::where('user_id',auth()->user()->id)->get();
                    foreach ($cartItems as $item) {
                         // Deduct mainstock quantity
                        $mainstock = MainStock::where('id', $item->main_stock_id)->first();
                        if ($mainstock) {
                            $mainstock->quantity -= $item->quantity;
                            $mainstock->save();
                        }

                        // Deduct privatebook quantity
                        $privatebook = PrivateBook::where('main_stock_id', $item->main_stock_id)->first();
                        if ($privatebook) {
                            $privatebook->quantity -= $item->quantity;
                            $privatebook->save();
                        }

                        //Update branch stock
                        $branchstock = BranchStock::where('branch_id', $data['branch_id'])
                        ->where('main_stock_id', $item->main_stock_id)
                        ->first();
                        if ($branchstock) {
                            $branchstock->quantity += $item->quantity;
                            $branchstock->save();
                        }
                        else{
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
                        // Create StockDistribute entry
                        StockDistribute::create([
                            'main_stock_id' => $item->main_stock_id,
                            'quantity' => $item->quantity,
                            'cost_price' => $item->cost_price,
                            'mrp' => $item->mrp,
                            'batch' => $item->batch,
                            'branch_id' => $data['branch_id'],
                        ]);

                        // Delete the cart item
                        $item->delete();
                    }
                    Notification::make()
                    ->success()
                    ->title('Item distributed')
                    ->color('success')
                    ->send();

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
            $data = $this->form->getState();
            $cartItem=StockDistributeCart::where('main_stock_id',$data['main_stock_id'])
           ->first();
            if($cartItem){
                $cartItem->quantity += $data['quantity'];
                $cartItem->update();
            }
            else{
                $mainStock = MainStock::where('item_id', $data['item_id'])->first();
                $newData = [
                    'cost_price'=> $mainStock->cost_price,
                    'mrp'=> $mainStock->mrp,
                    'batch'=> $mainStock->batch,
                    'main_stock_id' => $mainStock->id,
                ];
                $data += $newData;
                StockDistributeCart::create($data);
            }
            Notification::make()
            ->success()
            ->title('Item added')
            ->body('The item has been added to cart successfully.')
            ->color('success')
            ->send();
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
    public function getTabs(): array
    {
        if (auth()->user()->user_type == '1') {
            $branches = Branch::all();
            $tabs = [
                'All' => Tabs\Tab::make('All')
                    ->query(fn ($query) => $query), // Show all records
            ];

            foreach ($branches as $branch) {
                $tabs[$branch->branch_name] = Tabs\Tab::make($branch->branch_name)
                    ->query(fn ($query) => $query->where('branch_id', $branch->id));
            }

            return $tabs;
        }

        return [];
    }

}
