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
            ->numeric()
            ->summarize(Sum::make()->label('Total')),
            TextColumn::make('return_date')
            ->label('Payment date')
            ->date(),
            TextColumn::make('receiver_name'),
            TextColumn::make('address'),
            TextColumn::make('phone_number'),
        ])
        ->actions([
                DeleteAction::make()
                ->after(function (){
                    $this->dispatch('deleteRecord');
                })
                ->iconButton(),
                EditAction::make()
                ->iconButton()
                ->form([
                    TextInput::make('return_amount')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('receiver_name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('address')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('phone_number')
                        ->required()
                        ->maxLength(255),
                    DatePicker::make('return_date')
                ])
                ->after(function (){
                    $this->dispatch('editRecord');
                }),
                \Filament\Tables\Actions\Action::make('download receipt')
                ->button()
                ->icon('heroicon-o-arrow-down-tray')
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
                            TextInput::make('receiver_name')
                            ->required(),
                            TextInput::make('address')
                            ->required(),
                            TextInput::make('phone_number')
                            ->numeric()
                            ->required(),
                            DatePicker::make('return_date')
                            ->label('Payment date')
                            ->default(now())
                        ])->columns(2)
                        ])

                    ->label('Add Payment to Author/Submitter')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'margin',
                    ])
                    ->action(function (array $data, $record) {
                        $privateBookAccount = new PrivateBookAccount();
                        $privateBookAccount->private_book_id = $this->privateBookId;
                        $privateBookAccount->return_amount = $data['return_amount'];
                        $privateBookAccount->return_date = $data['return_date'];
                        $privateBookAccount->receiver_name = $data['receiver_name'];
                        $privateBookAccount->address = $data['address'];
                        $privateBookAccount->phone_number = $data['phone_number'];
                        $privateBookAccount->save();
                    })
                ]);
    }

    public function mount($privateBookId)
    {
        $this->privateBookId = $privateBookId;
    }
    public function render()
    {
        return view('livewire.private-book-payment');
    }
}
