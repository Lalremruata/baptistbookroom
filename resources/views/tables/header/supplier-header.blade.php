@php
    use App\Models\SupplierFinancials;
    use App\Models\Supplier;
    $openingBalance = Supplier::sum('opening_balance');
    $debits = SupplierFinancials::where('type', 'debit')->sum('amount');
    $credits = SupplierFinancials::where('type', 'credit')->sum('amount');
    // return $credits-$debits;
@endphp
<div class="p-4 text-red-500 text-xl bg-red-500 ">
    <p class="font-bold">
        Total Balance: {{  $openingBalance + ($credits - $debits) }}
    </p>
</div>

