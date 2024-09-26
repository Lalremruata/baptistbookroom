<?php

namespace App\Filament\Exports;

use App\Models\PrivateBook;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PrivateBookExporter extends Exporter
{
    protected static ?string $model = PrivateBook::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            // ExportColumn::make('mainStock.id'),
            ExportColumn::make('item.item_name'),
            ExportColumn::make('item.barcode')
            ->label('barcode'),
            ExportColumn::make('receive_from'),
            ExportColumn::make('author'),
            ExportColumn::make('file_no'),
            ExportColumn::make('quantity'),
            ExportColumn::make('created_at')
            ->label('date'),
            // ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your private book export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
