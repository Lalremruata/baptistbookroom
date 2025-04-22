<?php

namespace App\Filament\Resources;

use App\Filament\Exports\SaleExporter;
use App\Filament\Resources\SaleResource\Pages;
use App\Http\Controllers\SalesInvoicesController;
use App\Models\BranchStock;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
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
use Filament\Tables\Actions\BulkActionGroup;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Grouping\Group;

class SaleResource extends Resource
{
    protected static ?string $model = Sale::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Sales';
    protected static ?string $navigationLabel = 'Sales Report';

    public static function canCreate(): bool
    {
        return 0;
    }
    public static function getEloquentQuery(): Builder
    {
        $allowedRoles = ['Admin', 'Manager'];
        // return in_array(auth()->user()->roles->first()->title, $allowedRoles);
        if(in_array(auth()->user()->roles->first()->title, $allowedRoles)) {
            return parent::getEloquentQuery()->withoutGlobalScopes();
        }
        else {
            return parent::getEloquentQuery()->where('branch_id', auth()->user()->branch_id);

        }
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                ->schema([
                    Section::make()
                        ->schema([
                            Forms\Components\Select::make('branch_id')
                            ->relationship('branch', 'branch_name')
                            ->required(),
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required(),
                        ])->columns(2),

                    Section::make()
                    ->schema([
                        Forms\Components\Select::make('item_id')
                        ->relationship('item', 'item_name')
                        ->required(),
                        Forms\Components\DatePicker::make('sale_date'),
                        Forms\Components\TextInput::make('quantity')
                            ->numeric(),
                        Forms\Components\TextInput::make('cost_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('selling_price')
                            ->numeric(),
                        Forms\Components\TextInput::make('discount')
                            ->numeric(),
                    ])->columns(2)
                ])->columnSpan(['lg' => 3]),

            ])->columns(3);
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
                Tables\Columns\TextColumn::make('branchStock.mainStock.barcode')
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
                    ->label('Invoice Number')
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
                DeleteAction::make()
                    ->iconButton()
                    ->before(function (Model $record) {
                        $branchStock = BranchStock::where('branch_id', $record->branch_id)
                            ->where('id', $record->branch_stock_id)
                            ->first();
                        $branchStock->quantity += $record->quantity;
                        $branchStock->update();
                    }),
                Action::make('recalculate')
                    ->requiresConfirmation()
                    ->iconButton()
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Sale $record) {
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
                    TextInput::make('name')
                        ->autofocus()
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
                    // Fetch customer data based on the first record's customer_id
                    $customerId = $records[0]->customer_id;
                    $customer = Customer::find($customerId);

                    if (!$customer) {
                        return [];
                    }

                    // Return default values for the form fields
                    return [
                        'name' => $customer->customer_name,
                        'address' => $customer->address,
                        'phone' => $customer->phone,
                        'gst_number' => $customer->gst_number,
                    ];
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
                    ->exporter(SaleExporter::class)
                    ->formats([
                            ExportFormat::Xlsx,
                        ])
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('success')
            ], position: HeaderActionsPosition::Bottom);;
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
            'index' => Pages\ListSales::route('/'),
            // 'create' => Pages\CreateSale::route('/create'),
            // 'edit' => Pages\EditSale::route('/{record}/edit'),
        ];
    }
}
