<?php

namespace App\Filament\Exports;

use App\Models\BranchStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class BranchStockExporter extends Exporter
{
    protected static ?string $model = BranchStock::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('branch.branch_name'),
            ExportColumn::make('item.item_name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('cost_price'),
            ExportColumn::make('mrp'),
            ExportColumn::make('batch'),
            ExportColumn::make('barcode'),
            ExportColumn::make('item.gst_rate')
            ->label('GST Rate'),
            ExportColumn::make('item.hsn_number')
            ->label('HSN Number'),
            ExportColumn::make('created_at')
            ->label('date'),
            // ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your branch stock export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
