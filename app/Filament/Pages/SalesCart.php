<?php

namespace App\Filament\Pages;

use App\Models\BranchStock;
use App\Models\Item;
use App\Models\MainStock;
use App\Models\Sale;
use Filament\Actions\Action;
use App\Models\SalesCartItem;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Forms\Get;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
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
use Illuminate\Database\Eloquent\Builder;

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
    protected static ?string $navigationGroup = 'Manage Sales';

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
                Select::make('item')
                ->reactive()
                ->label('Item')
                ->options(function(){
                    if(auth()->user()->roles->contains('title', 'Admin'))
                    {
                        return MainStock::with('item')->get()->pluck('item.item_name', 'id')->toArray();
                    }
                    else{
                        return BranchStock::with('mainStock')->get()->pluck('mainStock.item.item_name', 'id')->toArray();
                    }
                })
                ->searchable()
                ->dehydrated()
                ->required(),
                TextInput::make('quantity')
                    ->reactive()
                    ->minValue(1)
                    ->maxValue(function (Get $get) {
                        $item = $get('item');
                        if ($item) {
                            if(auth()->user()->roles->contains('title', 'Admin')){
                                $result=MainStock::where('item_id',$item)
                                ->pluck('quantity','id')->first();
                                 return $result;
                            }
                            else{
                                $result=BranchStock::where('main_stock_id',$item)
                                ->where('branch_id',auth()->user()->branch_id)
                                ->pluck('quantity','id')->first();
                                 return $result;
                            }

                        }
                    })
                    ->required()
                    ->integer()
                    ->hint(function(Get $get){
                        $item = $get('item');
                        if ($item) {
                            if(auth()->user()->roles->contains('title', 'Admin')){
                                $result=MainStock::where('item_id',$item)
                                ->pluck('quantity','id')->first();
                                 return 'quantity available: '.$result;
                            }
                            else{
                                $result=BranchStock::where('main_stock_id',$item)
                                ->where('branch_id',auth()->user()->branch_id)
                                ->pluck('quantity','id')->first();
                                 return 'quantity available: '.$result;
                            }
                        }
                            return null;
                    })
                        ->hintColor('danger')
                        ->required(),
                Hidden::make('branch_id')
                    ->default(auth()->user()->branch_id),
                Hidden::make('user_id')
                    ->default(auth()->user()->id),
                    ]),

        ])->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(SalesCartItem::query()->where('branch_id', auth()->user()->branch_id))
            ->columns([
                TextColumn::make('mainStock.item.item_name'),
                TextColumn::make('quantity'),
                TextColumn::make('cost_price'),
                TextColumn::make('selling_price'),
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
                    $salesData = [];
                    $cartItems = SalesCartItem::where('branch_id',auth()->user()->branch_id)->get();
                    foreach ($cartItems as $item) {
                        $branchStock = BranchStock::where('branch_id', $item->branch_id)
                        ->where('item_id', $item->item_id)
                        ->first();
                        $branchStock->quantity -= $item->quantity;
                        $branchStock->update();
                        $salesData[] = [
                            'branch_id' => $item->branch_id,
                            'user_id' => auth()->user()->id,
                            'item_id' => $item->item_id,
                            'sale_date' => now(),
                            'cost_price' => $branchStock['cost_price'],
                            'selling_price' => $branchStock['mrp'],
                            'quantity' =>$item->quantity,
                        ];
                        $item->delete();
                    }
                    Sale::insert($salesData);
                })
                ->modalIcon('heroicon-o-check-circle')
                ->modalIconColor('danger')
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
            $mainStock = MainStock::where('item_id', $data['item'])->first();
            // if(auth()->user()->roles->contains('title', 'Admin')){
                $newData = [
                    'cost_price'=> $mainStock->cost_price,
                    'selling_price'=> $mainStock->mrp,
                ];
                $data += $newData;
                SalesCartItem::create($data);
            // }
            // else{
            //     $branchStock = BranchStock::with('mainStock')->where('mainStock.item_id', $data['item'])->first();
            //     dd($branchStock);
            //     SalesCartItem::create($data);
            // }
            $this->form->fill();
            // auth()->cartitem->save($data);
        } catch (Halt $exception) {
            return;
        }
    }

}
