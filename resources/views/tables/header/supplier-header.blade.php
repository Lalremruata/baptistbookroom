@php
use App\Models\SupplierFinancials;
    $debits = SupplierFinancials::where('type', 'debit')->sum('amount');
    $credits = SupplierFinancials::where('type', 'credit')->sum('amount');
    // return $credits-$debits;
@endphp
<div class="p-4 text-red-500 text-xl bg-red-500 underline">
    Total Balance {{$credits-$debits}}
</div>

