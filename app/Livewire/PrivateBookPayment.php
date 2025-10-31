<?php

namespace App\Livewire;

use App\Models\PrivateBookAccount;
use Filament\Actions\Action;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Livewire\Component;
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

class PrivateBookPayment extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;
    public $privateBookId;
    protected $listeners = ['refreshTables' => '$refresh'];

    protected function getTableQuery()
    {
        return PrivateBookAccount::query()
            ->where('private_book_id', $this->privateBookId);
    }
    public function table(Table $table): Table
    {
        return $table
        ->query(PrivateBookAccount::query()
        ->where('private_book_id', $this->privateBookId))
        ->columns([
            TextColumn::make('return_amount')
            ->label('Payment Amount')
            ->width('5%')
            ->summarize(Sum::make()),
            TextColumn::make('quantity'),
            TextColumn::make('return_date')
            ->label('Payment date')
            ->date(),
            TextColumn::make('receiver_name'),
            TextColumn::make('address'),
            TextColumn::make('phone_number'),
            TextColumn::make('payment_mode'),
            TextColumn::make('transaction_number'),
            TextColumn::make('account_number'),
            TextColumn::make('ifsc_code'),
            TextColumn::make('notes'),
        ])
        ->actions([
                DeleteAction::make()
                ->after(function (){
                    $this->dispatch('paymentUpdated');
                })
                ->iconButton(),
                EditAction::make()
                ->iconButton()
                ->form([
                    Section::make([
                        TextInput::make('return_amount')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('quantity')
                            ->numeric(),
                        TextInput::make('receiver_name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('pin_code')
                            ->required()
                            ->maxLength(6),
                        TextInput::make('phone_number')
                            ->required()
                            ->maxLength(10),
                        Textarea::make('notes'),
                    ])->columns(2),
                    Section::make([
                        Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer" => "bank transfer",
                                "cheque" => "cheque"
                            ])
                            ->reactive()
                            ->required(),
                        TextInput::make('transaction_number')
                            ->visible(fn($get) => $get('payment_mode') === 'upi')
                            ->reactive(),
                        TextInput::make('account_number')
                            ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                            ->reactive(),
                        TextInput::make('account_holder')
                            ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                            ->reactive(),
                        TextInput::make('branch_name')
                            ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                            ->reactive(),
                        TextInput::make('ifsc_code')
                            ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                            ->reactive(),
                    ]),
                ])
                ->after(function (){
                    $this->dispatch('paymentUpdated');
                }),
                \Filament\Tables\Actions\Action::make('print receipt')
                ->button()
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(function(PrivateBookAccount $privateBookAccount)
                {
                    return route('private-book-payment.receipt.download', $privateBookAccount);
                }),
        ])
                ->headerActions([
                    \Filament\Tables\Actions\CreateAction::make('add record')
                    ->form([
                        Section::make([
                            TextInput::make('return_amount')
                            ->label('Payment amount')
                            ->required(),
                            TextInput::make('quantity')
                            ->numeric(),
                            TextInput::make('receiver_name')
                            ->required(),
                            TextInput::make('address')
                            ->required(),
                            TextInput::make('pin_code')
                            ->required()
                            ->maxLength(6),
                            TextInput::make('phone_number')
                            ->numeric()
                            ->required()
                            ->maxLength(10),
                            DatePicker::make('return_date')
                            ->label('Payment date')
                            ->default(now()),
                            Textarea::make('notes'),
                        ])->columns(2),
                        Section::make([
                            Select::make('payment_mode')
                                ->options([
                                    "cash" => "cash",
                                    "upi" => "upi",
                                    "bank transfer" => "bank transfer",
                                    "cheque" => "cheque"
                                ])
                                ->reactive()
                                ->required(),
                            TextInput::make('transaction_number')
                                ->visible(fn($get) => $get('payment_mode') === 'upi')
                                ->reactive(),
                            TextInput::make('account_number')
                                ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                                ->reactive(),
                            TextInput::make('account_holder')
                                ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                                ->reactive(),
                            TextInput::make('branch_name')
                                ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                                ->reactive(),
                            TextInput::make('ifsc_code')
                                ->visible(fn($get) => $get('payment_mode') === 'bank transfer' || $get('payment_mode') === 'cheque')
                                ->reactive(),
                        ])
                        ])

                    ->label('Add Payment to Author/Submitter')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'margin',
                    ])
                    ->action(function (array $data, $record) {
                        // Call the addPayment function
                        $this->addPayment($data);
                    })
                    // ->after(fn()=>$this->dispatch('paymentUpdated'))
                ]);
    }

    public function mount($privateBookId)
    {
        $this->privateBookId = $privateBookId;
    }
    public function addPayment(array $data)
    {
        $privateBookAccount = new PrivateBookAccount();
        $privateBookAccount->private_book_id = $this->privateBookId;
        $privateBookAccount->return_amount = $data['return_amount'];
        $privateBookAccount->return_date = $data['return_date'];
        $privateBookAccount->receiver_name = $data['receiver_name'];
        $privateBookAccount->address = $data['address'];
        $privateBookAccount->phone_number = $data['phone_number'];
        $privateBookAccount->payment_mode = $data['payment_mode'];
        $privateBookAccount->transaction_number = $data['transaction_number'] ?? '';
        $privateBookAccount->account_number = $data['account_number'] ?? '';
        $privateBookAccount->account_holder = $data['account_holder'] ?? '';
        $privateBookAccount->branch_name = $data['branch_name'] ?? '';
        $privateBookAccount->pin_code = $data['pin_code'] ?? '';
        $privateBookAccount->quantity = $data['quantity'] ?? 0; // Set default quantity
        $privateBookAccount->ifsc_code = $data['ifsc_code'] ?? '';
        $privateBookAccount->notes = $data['notes'] ?? '';
        $privateBookAccount->save();

        // Emit an event after adding the payment
        $this->dispatch('paymentUpdated');
    }
    public function render()
    {
        return view('livewire.private-book-payment');
    }
}
