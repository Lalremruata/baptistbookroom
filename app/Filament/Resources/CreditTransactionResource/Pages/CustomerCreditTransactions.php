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

class CustomerCreditTransactions extends Page implements HasForms, HasTable, HasRecord
{
    protected static string $resource = CreditTransactionResource::class;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithRecord;

    // public CreditTransaction $record;
    protected static string $view = 'filament.resources.credit-transaction-resource.pages.customer-credit-transactions';
    public function mount(int | string $record): void
    {

        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
        // to pre populate fields
        $this->form->fill();
    }
    public function resolveRecord($record): ?CreditTransaction
    {
        return CreditTransaction::query()->where('id', $record)->first();
    }
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('recovered_amount')
            ])->statePath('data');
    }
    public function table(Table $table): Table
    {
        // dd($this->getRecord());
        return $table
            ->query(CreditTransaction::query()->where('id',$this->record->id))
            ->columns([
                TextColumn::make('customer.customer_name'),
                TextColumn::make('recieved_amount')
                ->label('Initial Recieved Amount'),
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
                // \Filament\Tables\Actions\Action::make('New Recovered Amount')
                // ->form([
                //     TextInput::make('recovered_amount')
                //     ,
                // ])
                // ->requiresConfirmation()
                // ->action(function (array $data) {
                //     $customerTransaction = CreditTransaction::query()->where('id', $this->record->id);
                //     $newTransaction = [];
                //     $newTransaction = [
                //         "recovered_amount" => $data['recovered_amount'],
                //         "total_amount" => $customerTransaction->total_amount,
                //         "recieved_amount" => $customerTransaction->recieved_amount,
                //     ];
                //     CreditTransaction::insert($newTransaction);
                // })
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
            $customer=CreditTransaction::where('customer_id', $this->record->id);
            $data = [
                'recieved_amount'=> $customer->recieved_amount,
                'total_amount'=> $customer->total_amount,
                'recovered_amount' =>$data['recovered_amount'],
            ];
            CreditTransaction::create($data);
        }
        catch(Halt $exception){
            return;
        }
    }

}
