<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Enums\Type;
use App\Filament\Resources\SupplierResource;
use App\Models\PaymentType;
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
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder;

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
        $balance = SupplierFinancials::query()
        ->where('supplier_id', $this->record->id)
        ->selectRaw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) - SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) AS balance')
        ->value('balance');

        return $table
            ->query(SupplierFinancials::query()->where('supplier_id', $this->record->id))
        // ->defaultGroup('voucher_no')
            ->columns([
                TextColumn::make('voucher_no'),
                TextColumn::make('amount')
                ->summarize(Summarizer::make()
                ->label('Balance')
                ->using(fn (Builder $query): string => $balance)),
                TextColumn::make('type')
                ->badge(),
                TextColumn::make('payment_mode'),
                TextColumn::make('transaction_number'),
                TextColumn::make('remarks'),
                TextColumn::make('created_at')
                ->label('date')
                ->date(),
            ])
            ->contentFooter(view('filament.pages.supplier-financial'))
            ->actions([
                DeleteAction::make(),
                EditAction::make()
                ->form([
                    Section::make([
                        TextInput::make('voucher_no')
                            ->autofocus()
                            ->required(),
                        TextInput::make('amount')
                            ->required(),
                        Select::make('type')
                            ->options(Type::class)
                            ->required(),
                        Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer"=>"bank transfer",
                                "cheque" => "cheque"
                            ])
                            ->required(),
                        TextInput::make('transaction_number'),
                        Textarea::make('remarks')
                        ])->columns(2)
                    ]),
                ])
            ->headerActions([
                \Filament\Tables\Actions\CreateAction::make('add record')
                ->form([
                    Section::make([
                        TextInput::make('voucher_no')
                        ->autofocus()
                        ->required(),
                    TextInput::make('amount')
                        ->required(),
                    Select::make('type')
                        ->options(Type::class)
                        ->required(),
                        Select::make('payment_mode')
                            ->options([
                                "cash" => "cash",
                                "upi" => "upi",
                                "bank transfer"=>"bank transfer",
                                "cheque" => "cheque"
                            ]),
                    TextInput::make('transaction_number'),
                    Textarea::make('remarks')
                    ])->columns(2)
                    ])

                ->label('add record')
                ->color('success')
                ->extraAttributes([
                    'class' => 'margin',
                ])
                ->action(function (array $data, $record) {
                    // $supplierFinancial = new SupplierFinancials();
                    $data = [
                        'supplier_id' => $this->record->id,
                        'voucher_no' => $data['voucher_no'],
                        'amount' => $data['amount'],
                        'type' => $data['type'],
                        'payment_mode' => $data['payment_mode'],
                        'transaction_number' => $data['transaction_number'],
                        'remarks' => $data['remarks'],
                    ];
                    SupplierFinancials::create($data);
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
