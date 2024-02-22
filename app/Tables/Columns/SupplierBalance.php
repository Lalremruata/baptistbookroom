<?php

namespace App\Tables\Columns;

use App\Models\Supplier;
use App\Models\SupplierFinancials;
use Filament\Tables\Columns\Column;

class SupplierBalance extends Column
{
    protected string $view = 'tables.columns.supplier-balance';
    // public Supplier $record;
    // public function getAccountBalance($accountId)
    // {
    //     $debits = SupplierFinancials::where('supplier_id', $this->record->id)->where('type', 'debit')->sum('amount');
    //     $credits = SupplierFinancials::where('supplier_id', $accountId)->where('type', 'credit')->sum('amount');

    //     return $debits - $credits;
    // }
}
