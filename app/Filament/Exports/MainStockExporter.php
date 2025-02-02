<?php

namespace App\Filament\Exports;

use App\Models\MainStock;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class MainStockExporter extends Exporter
{
    protected static ?string $model = MainStock::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('item.item_name'),
            ExportColumn::make('cost_price'),
            ExportColumn::make('mrp'),
            ExportColumn::make('batch'),
            ExportColumn::make('quantity'),
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
        $body = 'Your main stock export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
