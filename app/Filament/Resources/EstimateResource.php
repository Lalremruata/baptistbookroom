<?php

namespace App\Filament\Resources;

use App\Filament\Exports\EstimateExporter;
use App\Filament\Resources\EstimateResource\Pages;
use App\Http\Controllers\SalesInvoicesController;
use App\Models\Customer;
use App\Models\EstimateSale;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Grouping\Group;

class EstimateResource extends Resource
{
    protected static ?string $model = EstimateSale::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Sale Correction';
    protected static ?string $navigationLabel = 'Sale Correction Report';
    protected static ?string $modelLabel = 'Sale Correction';
    protected static ?string $pluralModelLabel = 'Sale Correction Report';

    protected static function isAdmin(): bool
    {
        return in_array(auth()->user()->roles->first()->title, ['Admin']);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::isAdmin();
    }

    public static function canViewAny(): bool
    {
        return static::isAdmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return static::isAdmin();
    }

    public static function canDelete(Model $record): bool
    {
        return static::isAdmin();
    }
    public static function getEloquentQuery(): Builder
    {
        // Admin-only resource — always show all branches.
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sale Correction Details')
                    ->schema([
                        Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->disabled()
                            ->dehydrated(),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('mrp')
                            ->label('MRP')
                            ->numeric(),
                        Forms\Components\TextInput::make('discount')
                            ->numeric(),
                        Forms\Components\TextInput::make('total_amount')
                            ->numeric()
                            ->required(),
                        Forms\Components\TextInput::make('gst_rate')
                            ->label('GST Rate (%)')
                            ->numeric(),
                        Forms\Components\TextInput::make('gst_amount')
                            ->label('GST Amount')
                            ->numeric(),
                        Forms\Components\TextInput::make('rate')
                            ->label('Taxable Amount')
                            ->numeric(),
                        Forms\Components\Select::make('payment_mode')
                            ->options([
                                'cash' => 'Cash',
                                'upi' => 'UPI',
                                'bank transfer' => 'Bank Transfer',
                                'cheque' => 'Cheque',
                            ]),
                        Forms\Components\TextInput::make('transaction_number'),
                        Forms\Components\DateTimePicker::make('created_at')
                            ->label('Sale Date'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->selectable()
        ->defaultGroup('memo')
        ->groups([
            Group::make('memo')
                ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderBy('created_at', 'desc'))
                ->label('Invoice Number'),
        ])
            ->columns([
                TextColumn::make('')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->rowIndex(),
                Tables\Columns\TextColumn::make('branch.branch_name')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('item.item_name')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.hsn_number')
                    ->label('HSN')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.category.category_name')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('item.subCategory.subcategory_name')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('item.barcode')
                    ->label('barcode')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->searchable(isIndividual: true)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('date')
                    ->date()
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('mrp')
                    ->label('MRP')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                Tables\Columns\TextColumn::make('discount')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('payment_mode')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('memo')
                    ->label('INVOICE NO')
                    ->state(fn (EstimateSale $record) => $record->getFormattedInvoiceNumber())
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('transaction_number')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gst_rate')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable(),
                Tables\Columns\TextColumn::make('gst_amount')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                Tables\Columns\TextColumn::make('rate')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                Tables\Columns\TextColumn::make('total_amount')
                    ->size(TextColumn\TextColumnSize::Medium)
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->summarize(Sum::make()->label('Total')),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('from'),
                        DatePicker::make('to'),
                    ])->columns(2)
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['to'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
                SelectFilter::make('branch')
                    ->relationship('branch', 'branch_name')
                    ->hidden(!auth()->user()->user_type == '1'),
                SelectFilter::make('category')
                    ->relationship('item.category', 'category_name'),
                SelectFilter::make('subCategory')
                    ->relationship('item.subCategory', 'subcategory_name'),
                SelectFilter::make('payment_mode')
                    ->options([
                        "cash" => "cash",
                        "upi" => "upi",
                        "bank transfer"=>"bank transfer",
                        "cheque" => "cheque"
                    ]),
            ], layout: FiltersLayout::AboveContent)->filtersFormColumns(4)
            ->actions([
                Tables\Actions\EditAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => in_array(auth()->user()->roles->first()->title, ['Admin'])),
                DeleteAction::make()
                    ->iconButton()
                    ->visible(fn (): bool => in_array(auth()->user()->roles->first()->title, ['Admin'])),
                Action::make('recalculate')
                    ->requiresConfirmation()
                    ->iconButton()
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (EstimateSale $record) {
                        $item = $record->item;
                        if ($item) {
                            $record->gst_rate = $item->gst_rate;

                            // Calculate total taxable amount (assuming total_amount is inclusive of GST)
                            $record->rate = $record->total_amount / (1 + ($record->gst_rate / 100));

                            // GST Amount (Total GST for all items)
                            $record->gst_amount = $record->total_amount - $record->rate;

                            $record->save();
                        }
                    })
            ])

            ->bulkActions([
                BulkAction::make('print invoice')
                ->button()
                ->icon('heroicon-o-printer')
                ->form([
                    DatePicker::make('invoice_date')
                        ->label('Invoice Date')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->helperText('Backdate the invoice if needed'),
                    TextInput::make('name')
                        ->required(),
                    TextInput::make('address'),
                    TextInput::make('phone'),
                    TextInput::make('gst_number')
                        ->label('GST Numbers'),
                ])
                ->fillForm(function (Collection $records) {
                    if ($records->isEmpty()) {
                        return [];
                    }
                    // Default the invoice date to the record's date (editable, so
                    // it can be backdated). Estimates carry no customer, so the
                    // buyer fields stay blank to fill in.
                    $defaults = [
                        'invoice_date' => $records[0]->created_at,
                    ];

                    $customer = Customer::find($records[0]->customer_id);

                    if ($customer) {
                        $defaults = array_merge($defaults, [
                            'name' => $customer->customer_name,
                            'address' => $customer->address,
                            'phone' => $customer->phone,
                            'gst_number' => $customer->gst_number,
                        ]);
                    }

                    return $defaults;
                })
                ->action(function (Collection $records, array $data) {
                    $saleController = new SalesInvoicesController();
                    return $saleController->generatePdf($records, $data);
                })
                ->deselectRecordsAfterCompletion(),
                BulkAction::make('recalculate')
                    ->requiresConfirmation()
                    ->color('success')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Collection $records) {
                        foreach ($records as $record) {
                            $item = $record->item;
                            if ($item) {
                                $record->gst_rate = $item->gst_rate;

                                // Calculate total taxable amount (assuming total_amount is inclusive of GST)
                                $record->rate = $record->total_amount / (1 + ($record->gst_rate / 100));

                                // GST Amount (Total GST for all items)
                                $record->gst_amount = $record->total_amount - $record->rate;

                                // Total Amount with GST remains unchanged
                                $record->total_amount_with_gst = $record->total_amount;

                                $record->save();
                            }
                        }
                    })
                    ->deselectRecordsAfterCompletion(),
        ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(EstimateExporter::class)
                    ->formats([
                            ExportFormat::Xlsx,
                        ])
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
            ], position: HeaderActionsPosition::Bottom);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEstimates::route('/'),
            'edit' => Pages\EditEstimate::route('/{record}/edit'),
        ];
    }
}
