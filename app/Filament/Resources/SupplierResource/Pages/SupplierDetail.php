<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Supplier;

use App\Models\SupplierFinancials;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Concerns\InteractsWithRecord;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Contracts\HasRecord;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
// use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Resources\Pages\ViewRecord;

class SupplierDetail extends Page implements HasForms, HasTable, HasRecord, HasActions
{
    use InteractsWithRecord;
    use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    protected static string $resource = SupplierResource::class;
    public SupplierFinancials $supplier;

    public ?array $data = [];

    protected static string $view = 'filament.pages.supplier-detail';


    public function mount(int | string $record): void
    {

        $this->record = $this->resolveRecord($record);

        static::authorizeResourceAccess();
        // to pre populate fields
        // $this->form->fill($record->toArray());
    }
    public function resolveRecord($record): ?Supplier
    {
        return Supplier::query()->where('id', $record)->first();
    }
    public function table(Table $table): Table
    {
        return $table
            ->query(SupplierFinancials::query()->where('supplier_id', $this->record->id))
            ->columns([
                TextColumn::make('supplier.supplier_name'),
                TextColumn::make('created_at')
                ->label('date'),
                TextColumn::make('bill'),
                TextColumn::make('credit'),
                TextColumn::make('debit'),
            ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make('add record')
                ->form([
                    TextInput::make('credit')
                        ->label('Credit')
                        ->required(),
                    TextInput::make('debit')
                        ->label('Debit')
                        ->required(),
                    TextInput::make('balance')
                        ->label('Balance')
                        ->required(),
                ])
                ->label('add record')
                ->color('success')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->action(function (array $data) {
                })
            ]);
    }
    public function deleteAction(Form $form): Action
    {
        return $form
        ->form([
            TextInput::make('credit')
                ->label('Credit')
                ->required(),
            TextInput::make('debit')
                ->label('Debit')
                ->required(),
            TextInput::make('balance')
                ->label('Balance')
                ->required(),
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
