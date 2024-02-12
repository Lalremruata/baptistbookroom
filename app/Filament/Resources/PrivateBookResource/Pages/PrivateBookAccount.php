<?php

namespace App\Filament\Resources\PrivateBookResource\Pages;

use App\Filament\Resources\PrivateBookResource;
use App\Models\PrivateBook;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\Page;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class PrivateBookAccount extends Page implements HasForms, HasTable,  HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    protected static string $resource = PrivateBookResource::class;
    public PrivateBook $record;
    public ?array $data = [];
    protected static string $view = 'filament.resources.private-book-resource.pages.private-book-account';
    public function mount(): void
    {
        $this->form->fill();
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(PrivateBookAccount::query()->where('private_book_id', $this->record->id))
            ->columns([
                TextColumn::make('bill_no'),
                TextColumn::make('credit'),
                TextColumn::make('debit'),
                TextColumn::make('created_at')
                ->label('date')
                ->date(),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make('add record')
                ->form([
                    Section::make([
                        TextInput::make('bill_no')
                        ->required(),
                    TextInput::make('credit')
                        ->label('Credit')
                        ->required(),
                    TextInput::make('debit')
                        ->label('Debit')
                        ->required(),
                    TextInput::make('balance')
                        ->label('Balance')
                        ->required(),
                    Textarea::make('remarks')
                    ])->columns(2)
                    ])

                ->label('add record')
                ->color('success')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->action(function (array $data, $record) {
                    $supplierFinancial = new PrivateBookAccount();
                    $supplierFinancial->supplier_id = $this->record->id;
                    $supplierFinancial->bill_no = $data['bill_no'];
                    $supplierFinancial->credit = $data['credit'];
                    $supplierFinancial->debit = $data['debit'];
                    $supplierFinancial->balance = $data['balance'];
                    $supplierFinancial->remarks = $data['remarks'];
                    $supplierFinancial->save();
                })
            ]);
    }
}
