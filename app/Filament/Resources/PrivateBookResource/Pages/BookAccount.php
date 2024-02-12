<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\PrivateBook;
use App\Models\PrivateBookAccount;

use Filament\Resources\Pages\Page;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class BookAccount extends Page implements HasForms, HasTable,  HasActions
{
    use InteractsWithTable;
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
    public function table(Table $table): Table
    {
        return $table
            ->query(PrivateBookAccount::query()->where('private_book_id', $this->record->id))
            ->columns([
                TextColumn::make('item.item_name'),
                TextColumn::make('return_amount'),
                TextColumn::make('created_at')
                ->label('date')
                ->date(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make('add record')
                ->form([
                    Section::make([
                        TextInput::make('return_amount')
                        ->required(),
                    ])->columns(2)
                    ])

                ->label('add record')
                ->color('success')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->action(function (array $data, $record) {
                    $privateBookAccount = new PrivateBookAccount();
                    $privateBookAccount->private_book_id = $this->record->id;
                    $privateBookAccount->return_amount = $data['return_amount'];
                    $privateBookAccount->return_date = now();
                    $privateBookAccount->save();
                })
            ]);
    }
}
