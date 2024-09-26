<?php

namespace App\Filament\Exports;

use App\Models\StockDistribute;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class StockDistributeExporter extends Exporter
{
    protected static ?string $model = StockDistribute::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('item.item_name'),
            ExportColumn::make('item.barcode')
            ->label('barcode'),
            ExportColumn::make('branch.branch_name'),
            ExportColumn::make('quantity'),
            ExportColumn::make('cost_price'),
            ExportColumn::make('mrp'),
            ExportColumn::make('batch'),
            ExportColumn::make('created_at')
                ->label('Transfer Date'),
            // ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock distribute export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
