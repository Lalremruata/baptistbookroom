<?php

namespace App\Filament\Exports;

use App\Models\Sale;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SaleExporter extends Exporter
{
    protected static ?string $model = Sale::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('item.item_name'),
            ExportColumn::make('item.subCategory.subcategory_name'),
            ExportColumn::make('item.barcode')
            ->label('barcode'),
            ExportColumn::make('branch.branch_name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('discount'),
            ExportColumn::make('total_amount'),
            ExportColumn::make('payment_mode'),
            ExportColumn::make('transaction_number'),
            ExportColumn::make('memo'),
            ExportColumn::make('created_at')
                ->label('date'),
            // ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your sale export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
