<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\CalculatesCartGst;
use App\Models\Branch;
use App\Models\EstimateMemo;
use App\Models\EstimateSale;
use App\Models\Item;
use App\Models\MainStock;
use Filament\Actions\Action;
use App\Models\EstimateCartItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Support\Enums\MaxWidth;
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
use Filament\Actions\StaticAction;
use Filament\Support\Enums\Alignment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class EstimateCart extends Page implements HasForms, HasTable, HasActions
{
    protected static ?string $model = EstimateCartItem::class;
    public EstimateCartItem $estimateCartItem;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    use CalculatesCartGst;
    public ?array $data = [];
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Sale Correction Cart';
    protected static ?string $navigationGroup = 'Sale Correction';
    protected static ?string $title = 'Sale Correction Cart';

    protected static string $view = 'filament.pages.estimate-cart';

    protected static function isAdmin(): bool
    {
        return in_array(auth()->user()->roles->first()->title, ['Admin']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAdmin();
    }

    public static function canAccess(): bool
    {
        return static::isAdmin();
    }

    public function mount(): void
    {
        abort_unless(static::isAdmin(), 403);
        $this->form->fill();
    }

    /**
     * Suggested per-unit price for an item, taken from its most recent
     * MainStock MRP (editable in the form). Estimates no longer read branch
     * stock, so this is only a convenience default.
     */
    private static function suggestedMrp(?int $itemId): float
    {
        if (! $itemId) {
            return 0;
        }

        return (float) (MainStock::where('item_id', $itemId)->orderByDesc('id')->value('mrp') ?? 0);
    }

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make()
            ->schema(array_filter([
                DateTimePicker::make('custom_created_at')
                    ->label('Sale Date')
                    ->default(now())
                    ->required()
                    ->native(false)
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->helperText('Set an earlier date/time to backdate this estimate'),

                Select::make('branch_id')
                    ->label('Branch')
                    ->options(Branch::pluck('branch_name', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive(),

                Select::make('item_id')
                    ->label('Item')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search) => Item::query()
                        ->where('item_name', 'like', "%{$search}%")
                        ->orWhere('barcode', 'like', "%{$search}%")
                        ->limit(50)
                        ->pluck('item_name', 'id'))
                    ->getOptionLabelUsing(fn ($value) => Item::find($value)?->item_name)
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $item = Item::find($state);
                        $gstRate = $item?->gst_rate ?? 0;
                        $mrp = static::suggestedMrp($state ? (int) $state : null);

                        $set('gst_rate', $gstRate);
                        $set('mrp', $mrp);

                        $quantity = (float) ($get('quantity') ?? 1);
                        $discount = (float) ($get('discount') ?? 0);
                        $totalMrp = $mrp * $quantity;
                        $discountedPrice = max(0, $totalMrp - $discount);
                        $taxableAmount = $gstRate > 0 ? $discountedPrice / (1 + ($gstRate / 100)) : $discountedPrice;
                        $set('gst_amount', number_format($discountedPrice - $taxableAmount, 2, '.', ''));
                    })
                    ->required()
                    ->reactive()
                    ->extraAttributes(['id' => 'estimate-item-field'])
                    ->hint(fn (Get $get) => $get('mrp') ? 'suggested mrp: ' . $get('mrp') : null)
                    ->hintColor('success'),

                TextInput::make('quantity')
                    ->numeric()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $price = (float) ($get('mrp') ?? 0);
                        $gstRate = (float) ($get('gst_rate') ?? 0);
                        $quantity = (float) ($state ?? 1);
                        $discount = (float) ($get('discount') ?? 0);

                        $totalMrp = $price * $quantity;
                        if ($discount > $totalMrp) {
                            $set('discount', $totalMrp);
                            $discount = $totalMrp;
                        }
                        $discountedPrice = max(0, $totalMrp - $discount);
                        $taxableAmount = $gstRate > 0 ? $discountedPrice / (1 + ($gstRate / 100)) : $discountedPrice;
                        $set('gst_amount', number_format($discountedPrice - $taxableAmount, 2, '.', ''));
                    }),

                TextInput::make('mrp')
                    ->label('Price (MRP)')
                    ->numeric()
                    ->step(0.01)
                    ->required()
                    ->reactive()
                    ->helperText('Suggested from stock — edit if needed')
                    ->afterStateUpdated(function ($state, callable $set, Get $get) {
                        $price = (float) ($state ?? 0);
                        $gstRate = (float) ($get('gst_rate') ?? 0);
                        $quantity = (float) ($get('quantity') ?? 1);
                        $discount = (float) ($get('discount') ?? 0);

                        $totalMrp = $price * $quantity;
                        $discountedPrice = max(0, $totalMrp - $discount);
                        $taxableAmount = $gstRate > 0 ? $discountedPrice / (1 + ($gstRate / 100)) : $discountedPrice;
                        $set('gst_amount', number_format($discountedPrice - $taxableAmount, 2, '.', ''));
                    }),

                TextInput::make('discount')
                    ->label('Discount Amount')
                    ->default(0)
                    ->numeric()
                    ->step(0.01)
                    ->helperText('Enter discount amount (not percentage)')
                    ->suffix('₹')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set, Get $get) {
                        $mrp = (float) ($get('mrp') ?? 0);
                        $quantity = (float) ($get('quantity') ?? 1);
                        $gstRate = (float) ($get('gst_rate') ?? 0);
                        $discount = (float) ($get('discount') ?? 0);

                        $totalMrp = $mrp * $quantity;
                        if ($discount > $totalMrp) {
                            $set('discount', $totalMrp);
                            $discount = $totalMrp;
                        }
                        $discountedPrice = max(0, $totalMrp - $discount);
                        $taxableAmount = $gstRate > 0 ? $discountedPrice / (1 + ($gstRate / 100)) : $discountedPrice;
                        $set('gst_amount', number_format($discountedPrice - $taxableAmount, 2, '.', ''));
                    }),

                TextInput::make('gst_amount')
                    ->label('GST Amount')
                    ->reactive()
                    ->disabled()
                    ->dehydrated(),
                TextInput::make('gst_rate')
                    ->label('GST Rate')
                    ->numeric()
                    ->reactive()
                    ->disabled()
                    ->dehydrated(),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                    ]))
                    ->columns(1)

        ])->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(EstimateCartItem::query()
                ->where('user_id', auth()->user()->id)
                ->with(['item', 'branch']))
            ->columns([
                TextColumn::make('branch.branch_name')
                    ->label('Branch')
                    ->sortable(),

                TextColumn::make('item.item_name')
                    ->label('Item')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->sortable()
                    ->alignCenter()
                    ->summarize(Summarizer::make()
                        ->using(fn(\Illuminate\Database\Query\Builder $query): string => $query->sum('quantity'))),

                TextColumn::make('discount')
                    ->label('Discount (₹)')
                    ->formatStateUsing(fn (string $state): string => '₹' . number_format($state, 2))
                    ->alignCenter(),

                TextColumn::make('selling_price')
                    ->label('Selling Price')
                    ->formatStateUsing(fn (string $state): string => '₹' . number_format($state, 2))
                    ->alignCenter()
                    ->summarize(Summarizer::make()
                        ->using(fn(\Illuminate\Database\Query\Builder $query): string => '₹' . number_format($query->sum('selling_price'), 2))),

                TextColumn::make('gst_amount')
                    ->label('GST')
                    ->formatStateUsing(fn (string $state): string => '₹' . number_format($state, 2))
                    ->alignCenter()
                    ->summarize(Summarizer::make()
                        ->using(fn(\Illuminate\Database\Query\Builder $query): string => '₹' . number_format($query->sum('gst_amount'), 2))),

                TextColumn::make('total_amount_with_gst')
                    ->label('Total Amount')
                    ->formatStateUsing(fn (string $state): string => '₹' . number_format($state, 2))
                    ->alignCenter()
                    ->summarize(Summarizer::make()
                        ->using(fn(\Illuminate\Database\Query\Builder $query): string => '₹' . number_format($query->sum('total_amount_with_gst'), 2))),

                TextColumn::make('created_at')
                    ->label('Added On')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                DeleteAction::make()
                    ->after(fn () => $this->dispatch('focusItemSearch'))
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('checkout cart')
                ->steps([
                    Step::make('Payment')
                    ->schema([
                        Section::make([
                            TextInput::make('received_amount')
                            ->prefix('₹')
                            ->numeric()
                            ->required()
                            ->hint(function(){
                                $totalAmount = EstimateCartItem::where('user_id', auth()->user()->id)->sum('selling_price');
                                return 'Amount : ' . $totalAmount;
                            })
                            ->default(function(){
                                return EstimateCartItem::where('user_id', auth()->user()->id)->sum('selling_price');
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
                    try {
                        DB::transaction(function () use ($data) {
                            $cartItems = EstimateCartItem::where('user_id', auth()->user()->id)->get();

                            // Estimate memo is per branch (isolated counter, never touches real `memos`)
                            $memoByBranch = [];

                            foreach ($cartItems as $item) {
                                $branchId = $item->branch_id;

                                if (! isset($memoByBranch[$branchId])) {
                                    $lastMemo = EstimateMemo::where('branch_id', $branchId)
                                        ->lockForUpdate()
                                        ->orderBy('memo', 'desc')
                                        ->first();

                                    $newMemoNumber = $lastMemo ? $lastMemo->memo + 1 : 1;

                                    EstimateMemo::updateOrCreate(
                                        ['branch_id' => $branchId, 'memo' => $lastMemo ? $lastMemo->memo : null],
                                        ['memo' => $newMemoNumber]
                                    );

                                    $memoByBranch[$branchId] = $newMemoNumber;
                                }

                                // Estimate sale entry (dummy sales table, no stock touched)
                                EstimateSale::create([
                                    'item_id' => $item->item_id,
                                    'branch_id' => $branchId,
                                    'user_id' => auth()->user()->id,
                                    'customer_id' => null,
                                    'quantity' => $item->quantity,
                                    'mrp' => $item->mrp,
                                    'discount' => $item->discount,
                                    'total_amount' => $item->selling_price,
                                    'gst_rate' => $item->gst_rate,
                                    'gst_amount' => $item->gst_amount,
                                    'rate' => $item->rate,
                                    'total_amount_with_gst' => $item->total_amount_with_gst,
                                    'payment_mode' => $data['payment_mode'],
                                    'transaction_number' => $data['transaction_number'],
                                    'memo' => $memoByBranch[$branchId],
                                    'created_at' => $item->created_at,
                                    'updated_at' => now(),
                                ]);

                                $item->delete();
                            }
                        });

                        Notification::make()
                            ->success()
                            ->title('Sale correction saved successfully!')
                            ->color('success')
                            ->send();
                    } catch (\Exception $e) {
                        Log::error('Sale correction checkout failed: ' . $e->getMessage());

                        Notification::make()
                            ->danger()
                            ->title('Failed to save sale correction!')
                            ->body('An error occurred during the checkout process.')
                            ->send();
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
                $branchId = $data['branch_id'] ?? null;

                // Sale Date drives the estimate's created_at, so it can be backdated.
                $createdAt = $data['custom_created_at'] ?? now();

                // Accumulate same item + branch into one line for this user
                $cartItem = EstimateCartItem::where('item_id', $data['item_id'])
                    ->where('user_id', auth()->user()->id)
                    ->where('branch_id', $data['branch_id'])
                    ->first();

                $mrp = $data['mrp'] ?? 0;
                $gstCalc = $this->calculateGst(
                    (float) $mrp,
                    (int) $data['quantity'],
                    (float) ($data['discount'] ?? 0),
                    (float) ($data['gst_rate'] ?? 0)
                );

                if (!$cartItem) {
                    $data = array_merge($data, $gstCalc, [
                        'cost_price'  => 0,
                        'mrp'         => $mrp,
                        'created_at'  => $createdAt,
                        'updated_at'  => now(),
                    ]);
                    EstimateCartItem::create($data);
                } else {
                    $cartItem->quantity               += $data['quantity'];
                    $cartItem->selling_price          += $gstCalc['selling_price'];
                    $cartItem->gst_amount             += $gstCalc['gst_amount'];
                    $cartItem->rate                   += $gstCalc['rate'];
                    $cartItem->total_amount_with_gst  += $gstCalc['total_amount_with_gst'];
                    $cartItem->discount               += $gstCalc['discount'];
                    $cartItem->mrp                     = $mrp;
                    $cartItem->created_at              = $createdAt;
                    $cartItem->update();
                }
                // Reset the form but keep the selected branch, so several items
                // can be added for the same branch without re-picking it. Then
                // move focus back to the item search.
                $this->form->fill();
                if ($branchId) {
                    $this->data['branch_id'] = $branchId;
                }
                // Keep the chosen Sale Date too, so a backdated batch stays backdated.
                $this->data['custom_created_at'] = \Carbon\Carbon::parse($createdAt)->format('Y-m-d H:i:s');
                $this->dispatch('focusItemSearch');

                Notification::make()
                    ->success()
                    ->title('Item added')
                    ->body('The item has been added to cart successfully.')
                    ->color('success')
                    ->send();
            });

        }   catch (\Exception $e) {
            Log::error('Item added to estimate cart failed: ' . $e->getMessage());

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
            ->slideOver()
            ->modalCancelAction(fn (StaticAction $action) => $action->label('Close'))
            ->modalSubmitAction(false)
            ->modalWidth(MaxWidth::Medium)
            ->modalAlignment(Alignment::Center)
        ];
    }

}
