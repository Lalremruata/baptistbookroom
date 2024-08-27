<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\PrivateBook;
use App\Models\PrivateBookAccount;

use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\Page;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Table;
use Filament\Support\Enums\MaxWidth;
class BookAccount extends Page implements HasForms,  HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
     protected static string $resource = PrivateBookResource::class;

     public PrivateBook $record;
     public ?array $data = [];
    protected static string $view = 'filament.resources.private-book-resource.pages.book-account';
    public function mount(): void
    {
        $this->form->fill();
    }
    // public function table(Table $table): Table
    // {
    //     return $table
    //         ->query(PrivateBookAccount::query()->where('private_book_id', $this->record->id))
    //         ->columns([
    //             TextColumn::make('return_amount')
    //             ->label('Payment Amount')
    //             ->width('5%')
    //             ->numeric(),
    //             TextColumn::make('date')
    //             ->label('date')
    //             ->date(),
    //         ])
    //         ->headerActions([
    //             \Filament\Tables\Actions\CreateAction::make('add record')
    //             ->form([
    //                 Section::make([
    //                     TextInput::make('return_amount')
    //                     ->label('Payment amount')
    //                     ->required(),
    //                     DatePicker::make('return_date')
    //                     ->label('Payment date')
    //                     ->default(now())
    //                 ])->columns(2)
    //                 ])

    //             ->label('Payment')
    //             ->color('success')
    //             ->extraAttributes([
    //                 'class' => 'margin',
    //             ])
    //             ->action(function (array $data, $record) {
    //                 $privateBookAccount = new PrivateBookAccount();
    //                 $privateBookAccount->private_book_id = $this->record->id;
    //                 $privateBookAccount->return_amount = $data['return_amount'];
    //                 $privateBookAccount->return_date = $data['return_date'];
    //                 $privateBookAccount->save();
    //             })
    //         ]);
    // }
    public function getMaxContentWidth(): MaxWidth
    {
        return MaxWidth::FiveExtraLarge;
    }
}
