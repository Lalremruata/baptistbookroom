<div>
    @php
    use App\Models\SupplierFinancials;
    $debits = SupplierFinancials::where('supplier_id', $getRecord()->id)->where('type', 'debit')->sum('amount');
    $credits = SupplierFinancials::where('supplier_id', $getRecord()->id)->where('type', 'credit')->sum('amount');
    @endphp
    {{  $credits - $debits }}
</div>
