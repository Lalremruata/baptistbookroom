<?php

namespace App\Livewire;

use App\Models\PrivateBookAccount;
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
            ->label('Book returned')
            ->width('5%')
            ->numeric()
            ->summarize(Sum::make()->label('Total')),
            TextColumn::make('return_date')
            ->label('Return date')
            ->date(),
        ])
        ->actions([
                DeleteAction::make()
                ->after(function (){
                    $this->dispatch('deleteRecord');
                })
                ->iconButton(),
                EditAction::make()
                ->iconButton()
                ->after(function (){
                    $this->dispatch('editRecord');
                })
        ])
                ->headerActions([
                    \Filament\Tables\Actions\CreateAction::make('add record')
                    ->form([
                        Section::make([
                            TextInput::make('return_amount')
                            ->label('Book returned amount')
                            ->required(),
                            DatePicker::make('return_date')
                            ->label('Return date')
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
                        $privateBookAccount->private_book_id = $this->record->id;
                        $privateBookAccount->return_amount = $data['return_amount'];
                        $privateBookAccount->return_date = $data['return_date'];
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
