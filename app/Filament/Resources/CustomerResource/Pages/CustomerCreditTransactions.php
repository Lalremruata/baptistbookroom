<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CreditTransactionResource;
use App\Filament\Resources\CustomerResource;
use App\Models\CreditTransaction;
use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Resources\Pages\Page;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Exceptions\Halt;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder;

class CustomerCreditTransactions extends Page implements HasForms, HasTable, HasActions
{
    protected static string $resource = CustomerResource::class;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    public ?array $postData = [];
    public ?array $data = [];
    public Customer $record;
    protected static string $view = 'filament.resources.customer-resource.pages.customer-credit-transactions';
    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('recovered_amount')
                ->prefix('₹')
                ->autofocus()
            ])->columns(2)->statePath('data');
    }
    public function table(Table $table): Table
    {
        $customerId = $this->record->id;

        $creditTransactions = CreditTransaction::where('customer_id', $customerId)
        ->skip(1)  // Skip the first record
        ->take(PHP_INT_MAX);
            // dd($creditTransactions->toSql());
        return $table
            ->query($creditTransactions)
            ->columns([
                // TextColumn::make('customer.customer_name'),
                TextColumn::make('recovered_amount')
                ->summarize(Sum::make()->label('Total')),
                TextColumn::make('updated_at')
                ->label('Received date')
                ->date(),
                // TextColumn::make('total_amount'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                DeleteAction::make(),
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
                //     $customerTransaction = CreditTransaction::query()->where('customer_id', $this->record->id)->first();
                //     $newTransaction = [];
                //     $newTransaction = [
                //         "customer_id" => $customerTransaction->customer_id,
                //         "recovered_amount" => $data['recovered_amount'],
                //         "total_amount" => $customerTransaction->total_amount,
                //         "received_amount" => $customerTransaction->received_amount,
                //     ];
                //     CreditTransaction::create($newTransaction);
                // })
            ]);
    }
    // public static function getPages(): array
    // {
    //     return [
    //         'edit' => CreditTransactionResource\Pages\EditCreditTransaction::route('/{record}/edit'),
    //     ];
    // }
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
            $customerTransaction = CreditTransaction::query()->where('customer_id', $this->record->id)->first();
            $newTransaction = [];
            $newTransaction = [
                "customer_id" => $customerTransaction->customer_id,
                "recovered_amount" => $data['recovered_amount'],
                "total_amount" => $customerTransaction->total_amount,
                "received_amount" => $customerTransaction->received_amount,
            ];
            CreditTransaction::create($newTransaction);
            $this->form->fill();
        }
        catch(Halt $exception){
            return;
        }
    }
}
