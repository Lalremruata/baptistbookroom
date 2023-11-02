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
                        ->searchable()
                        ->label('Item')
                        ->options(MainStock::with('item')->get()->pluck('item.item_name', 'item_id')->toArray())
                        ->afterStateUpdated(fn(callable $set,Get $get)=>$set('main_stock_id',MainStock::query()
                            ->where('item_id', $get('item_id'))->pluck('id')->first()))
                        ->required()
                        ->dehydrated(),
                    TextInput::make('quantity')
                    ->reactive()
                    ->required()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $itemId = $get('item_id');
                        // dd($itemId);
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
                    Hidden::make('main_stock_id'),

                ])->columns(2)

            ])
            ->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(StockDistributeCart::query())
            ->columns([
                TextColumn::make('mainStock.item.item_name'),
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
                        $mainstock = MainStock::where('id', $item->main_stock_id)->first();
                        $mainstock->quantity -= $item->quantity;
                        $mainstock->update();

                        $branchstock = BranchStock::where('branch_id', $data['branch_id'])
                        ->where('main_stock_id', $item->main_stock_id)
                        ->first();
                        if ($branchstock) {
                            $branchstock->quantity += $item->quantity;
                            $branchstock->update();
                        }
                        else{
                            $branchstock = new Branchstock();
                            $branchstock->main_stock_id = $item->main_stock_id;
                            $branchstock->quantity = $item->quantity;
                            $branchstock->cost_price = $mainstock->cost_price;
                            $branchstock->branch_id = $data['branch_id'];
                            $branchstock->batch = $mainstock->batch;
                            $branchstock->mrp = $mainstock->mrp;
                            $branchstock->save();
                        }
                        $stockdistribute = new StockDistribute();
                        $stockdistribute->main_stock_id = $item->main_stock_id;
                        $stockdistribute->quantity = $item->quantity;
                        $stockdistribute->cost_price = $item->cost_price;
                        $stockdistribute->mrp = $item->mrp;
                        $stockdistribute->batch = $item->batch;
                        $stockdistribute->branch_id = $data['branch_id'];
                        $stockdistribute->save();

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
                ->submit('save'),
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
}
