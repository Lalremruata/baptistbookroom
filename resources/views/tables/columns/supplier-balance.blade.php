<div>
    @php
    use App\Models\SupplierFinancials;
    use App\Models\Supplier;
    $openingBalance = Supplier::where('id', $getRecord()->id)->pluck('opening_balance')->first();
    $debits = SupplierFinancials::where('supplier_id', $getRecord()->id)->where('type', 'debit')->sum('amount');
    $credits = SupplierFinancials::where('supplier_id', $getRecord()->id)->where('type', 'credit')->sum('amount');
    @endphp
    {{  $openingBalance + ($credits - $debits) }}
</div>
