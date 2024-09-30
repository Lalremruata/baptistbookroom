<?php

namespace App\Livewire;

use App\Models\MainStock;
use App\Models\PrivateBook;
use App\Models\PrivateBookReturn;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Model;
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

class PrivateBookReturns extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithTable;
    use InteractsWithActions;
    public $privateBookId;
    protected $listeners = ['refreshTables' => '$refresh'];

    protected function getTableQuery()
    {
        return PrivateBookReturn::query()
            ->where('private_book_id', $this->privateBookId);
    }
    public function table(Table $table): Table
    {
        return $table
        ->query(PrivateBookReturn::query()
        ->where('private_book_id', $this->privateBookId))
        ->columns([
            TextColumn::make('return_amount')
            ->label('Book returned Quantity')
            ->width('5%')
            ->numeric()
            ->summarize(Sum::make()->label('Total')),
            TextColumn::make('return_date')
            ->dateTime()
            ->label('Return date')
            ->date(),
            TextColumn::make('receiver_name'),
            TextColumn::make('address'),
            TextColumn::make('phone_number'),
        ])
        ->actions([
                DeleteAction::make()
                ->before(function (Model $record){
                    //Incriment PrivateBook Stock Quantity
                    $privateBook = PrivateBook::where('id',  $this->privateBookId)
                    ->first();
                    $privateBook->quantity += $record->return_amount;
                    $privateBook->update();
                    //Deduct MainStock Quantity
                    $mainStock = MainStock::where('id',  $privateBook->main_stock_id)
                    ->first();
                    $mainStock->quantity += $record->return_amount;
                    $mainStock->update();
                })
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
                            ->label('Book returned quantity')
                            ->required(),
                            TextInput::make('receiver_name')
                            ->required(),
                            TextInput::make('address')
                            ->required(),
                            TextInput::make('phone_number')
                            ->numeric()
                            ->required(),
                            DatePicker::make('return_date')
                            ->label('Return date')
                            ->default(now())
                        ])->columns(2)
                        ])

                    ->label('Add Book Returned')
                    ->color('success')
                    ->extraAttributes([
                        'class' => 'margin',
                    ])
                    ->before(function (array $data, $record){
                        //Deduct PrivateBook Stock Quantity
                        $privateBook = PrivateBook::where('id',  $this->privateBookId)
                        ->first();
                        $privateBook->quantity -= $data['return_amount'];
                        $privateBook->update();
                        //Deduct MainStock Quantity
                        $mainStock = MainStock::where('id',  $privateBook->main_stock_id)
                        ->first();
                        $mainStock->quantity -= $data['return_amount'];
                        $mainStock->update();
                    })
                    ->action(function (array $data, $record) {
                        $privateBookAccount = new PrivateBookReturn();
                        $privateBookAccount->private_book_id = $this->privateBookId;
                        $privateBookAccount->return_amount = $data['return_amount'];
                        $privateBookAccount->return_date = $data['return_date'];
                        $privateBookAccount->return_date = $data['receiver_name'];
                        $privateBookAccount->return_date = $data['address'];
                        $privateBookAccount->return_date = $data['phone_number'];
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
        return view('livewire.private-book-returns');
    }
}
