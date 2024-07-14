<?php

namespace App\Livewire;

use App\Models\SupplierFinancials;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;
use App\Enums\Type;

class SupplierCreditTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;

    public $supplierId;
    protected $listeners = ['refreshTables' => '$refresh'];

    protected function getTableQuery()
    {
        return SupplierFinancials::query()
            ->where('supplier_id', $this->supplierId)
            ->where('type', 'credit');
    }

    public function table(Table $table): Table
    {
        return $table
        ->query(SupplierFinancials::query()
            ->where('supplier_id', $this->supplierId)->where('type', 'credit'))
        ->columns([
            TextColumn::make('voucher_no'),
            TextColumn::make('amount')
            ->numeric()
            ->summarize(Sum::make()->label('Total')),
            TextColumn::make('type')
                ->sortable()
                ->badge(),
            TextColumn::make('payment_mode'),
            TextColumn::make('transaction_number'),
            TextColumn::make('remarks'),
            TextColumn::make('created_at')
                ->label('date')
                ->date(),
        ])
        ->actions([
                DeleteAction::make()
                ->after(function (){
                    $this->dispatch('deleteRecord');
                }),
                EditAction::make()
                ->after(function (){
                    $this->dispatch('editRecord');
                })
                ->form([
                    Section::make([
                        TextInput::make('voucher_no')
                            ->autofocus()
                            ->required(),
                        TextInput::make('amount')
                            ->required(),
                        // Select::make('type')
                        //     ->options(Type::class)
                        //     ->required(),
                        Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer"=>"bank transfer",
                                "cheque" => "cheque"
                            ])
                            ->required(),
                        TextInput::make('transaction_number'),
                        Textarea::make('remarks')
                        ])->columns(2)
                    ]),
                ]);
    }
    public function mount($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    public function render()
    {
        return view('livewire.supplier-credit-table');
    }
}
