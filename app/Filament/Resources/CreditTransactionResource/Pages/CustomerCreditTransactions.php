<?php

namespace App\Filament\Resources\CreditTransactionResource\Pages;

use App\Filament\Resources\CreditTransactionResource;
use App\Models\CreditTransaction;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Resources\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Actions\Contracts\HasRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Table;
use Illuminate\Http\Request;
use Livewire\Component;

class CustomerCreditTransactions extends Page implements HasForms, HasTable
{
    protected static string $resource = CreditTransactionResource::class;
    use InteractsWithTable;
    use InteractsWithForms;
    // use InteractsWithRecord;
    public ?array $data = [];

    public CreditTransaction $record;
   
    protected static string $view = 'filament.resources.credit-transaction-resource.pages.customer-credit-transactions';
    public function mount(): void
    {
        $this->form->fill();
    }
    // public function mount(int | string $record): void
    // {

    //     $this->record = $this->resolveRecord($record);

    //     static::authorizeResourceAccess();
    //     // to pre populate fields
    //     $this->form->fill();
    // }
    // public function resolveRecord($record): ?CreditTransaction
    // {
    //     return CreditTransaction::query()->where('id', $record)->first();
    // }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('recovered_amount')
            ])->statePath('data');
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(CreditTransaction::query()->where('customer_id',$this->record->customer_id))
            ->columns([
                TextColumn::make('customer.customer_name'),
                TextColumn::make('received_amount')
                ->label('Initial Received Amount'),
                TextColumn::make('total_amount'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ])
            ->headerActions([
                \Filament\Tables\Actions\Action::make('New Recovered Amount')
                ->form([
                    TextInput::make('recovered_amount')
                    ,
                ])
                ->requiresConfirmation()
                ->action(function (array $data) {
                    $customerTransaction = CreditTransaction::query()->where('id', $this->record->id)->first();
                    $newTransaction = [];
                    $newTransaction = [
                        "customer_id" => $customerTransaction->customer_id,
                        "recovered_amount" => $data['recovered_amount'],
                        "total_amount" => $customerTransaction->total_amount,
                        "received_amount" => $customerTransaction->received_amount,
                    ];
                    CreditTransaction::insert($newTransaction);
                })
            ]);
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label(__('update'))
                ->submit('save'),
        ];
    }
    public function save(): void
    {
        try {
            $data = $this->form->getState();
            $customerTransaction = CreditTransaction::query()->where('id', $this->record->id)->first();
            $newTransaction = [];
            $newTransaction = [
                "customer_id" => $customerTransaction->customer_id,
                "recovered_amount" => $data['recovered_amount'],
                "total_amount" => $customerTransaction->total_amount,
                "received_amount" => $customerTransaction->received_amount,
            ];
            CreditTransaction::create($newTransaction);
        }
        catch(Halt $exception){
            return;
        }
    }

}
