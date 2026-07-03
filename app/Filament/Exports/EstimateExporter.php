<?php

namespace App\Filament\Exports;

use App\Models\EstimateSale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class EstimateExporter extends Exporter
{
    protected static ?string $model = EstimateSale::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('created_at')
            ->label('Date'),
            ExportColumn::make('memo')
            ->label('INVOICE NO')
            ->state(fn (EstimateSale $record) => $record->getFormattedInvoiceNumber()),
            ExportColumn::make('item.hsn_number')
            ->label('HSN'),
            ExportColumn::make('item.item_name')
            ->label('Item Description'),
            ExportColumn::make('item.category.category_name'),
            ExportColumn::make('item.subCategory.subcategory_name'),
            ExportColumn::make('item.barcode')
            ->label('barcode'),
            ExportColumn::make('branch.branch_name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('mrp')
            ->label('MRP'),
            ExportColumn::make('discount'),
            ExportColumn::make('rate'),
            ExportColumn::make('gst_amount'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('rate')
            ->label('Taxable Amount'),
            ExportColumn::make('payment_mode'),
            ExportColumn::make('transaction_number'),
            ExportColumn::make('gst_rate'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your sale correction export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }

}
