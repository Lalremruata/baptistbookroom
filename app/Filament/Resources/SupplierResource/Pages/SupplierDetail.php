<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Supplier;

use App\Models\SupplierFinancials;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Actions\Concerns\InteractsWithActions;;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
// use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Actions\DeleteAction as ActionsDeleteAction;

class SupplierDetail extends Page implements HasForms, HasTable,  HasActions
{
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    protected static string $resource = SupplierResource::class;
    public Supplier $record;

    public ?array $data = [];

    protected static string $view = 'filament.pages.supplier-detail';
    public function mount(): void
    {
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(SupplierFinancials::query()->where('supplier_id', $this->record->id))
            ->columns([
                TextColumn::make('bill_no'),
                TextColumn::make('credit'),
                TextColumn::make('debit'),
                TextColumn::make('balance'),
                TextColumn::make('remarks'),
                TextColumn::make('created_at')
                ->label('date')
                ->date(),
            ])
            ->actions([
                DeleteAction::make(),
                EditAction::make()
                ->form([
                    Section::make([
                        TextInput::make('bill_no')
                        ->autofocus()
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
                    ]),
                ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make('add record')
                ->form([
                    Section::make([
                        TextInput::make('bill_no')
                        ->autofocus()
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
                    $supplierFinancial = new SupplierFinancials();
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
    // protected function getActions(): array
    // {
    //     return [
    //         Action::make('Withdraw')
    //         // ->mountUsing(fn (Forms\ComponentContainer $form) => $form->fill([
    //         //     'Name' => $this->record->name,
    //         // ]))
    //         ->successNotification(
    //             Notification::make()
    //                  ->success()
    //                  ->title('User withdrawed')
    //                  ->body('The user has withdrawed successfully.'),
    //          )
    //         ];
    //         }
}
