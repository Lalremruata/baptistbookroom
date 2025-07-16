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
use Filament\Forms\Components\DatePicker;
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
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
// use Illuminate\Database\Query\Builder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;

class SupplierDetail extends Page implements HasForms,  HasActions
{

    // use InteractsWithTable;
    use InteractsWithForms;
    use InteractsWithActions;
    protected static string $resource = SupplierResource::class;
    public Supplier $record;

    public ?array $data = [];
    protected $listeners = ['deleteRecord' => 'balance', 'editRecord' => 'balance'];
    public $balance;

    protected static string $view = 'filament.pages.supplier-detail';

    public static function canCreate(): bool
    {
        return 0;
    }
    public function mount(): void
    {
        $this->form->fill();
        $this->balance();
    }

    protected function getActions(): array
    {
        return [
            Action::make('AddRecord')
                ->visible(fn (): bool => auth()->user()->can('create', static::$resource::getModel()))
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
                DatePicker::make('remarks')
                ])->columns(2)
                ])

            ->label('add record')
            ->color('success')
            ->extraAttributes([
                'class' => 'margin',
            ])
            ->action(function (array $data, $record) {
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
                $this->balance();
                $this->dispatch('refreshTables');
            })
            ->successNotification(
                Notification::make()
                     ->success()
                     ->title('Record Saved')
                     ->body('The record saved successfully.'),
             )
            ];
        }
        public function balance()
        {
            $openingBalance = Supplier::where('id', $this->record->id)->pluck('opening_balance')->first();
            $debits = SupplierFinancials::where('supplier_id', $this->record->id)->where('type', 'debit')->sum('amount');
            $credits = SupplierFinancials::where('supplier_id', $this->record->id)->where('type', 'credit')->sum('amount');
            $this->balance= $openingBalance + ($credits - $debits);
        }
        public function getTitle(): string | Htmlable
        {
            /** @var Supplier */
            $record = $this->record;

            return $record->supplier_name;

        }
}
