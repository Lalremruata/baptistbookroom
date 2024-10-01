@php
        $value=App\Models\CreditTransaction::where('customer_id', $this->record->id)->first();
        $sum=App\Models\CreditTransaction::where('customer_id', $this->record->id)->sum('recovered_amount');
        $remainingCredit = $value->total_amount - ($sum+$value->received_amount);
    @endphp
<x-filament-panels::page>
<x-filament::section collapsible icon="heroicon-o-user"
    icon-color="info" icon-size="lg">
    <x-slot name="heading">
    <h1 class="text-xl font-bold">Customer Name : {{ $this->record->customer_name }}</h1>
    </x-slot>
         <h5 class="text-xl font-bold">Total Amount: {{ $value->total_amount }}</h5>
         <h5 class="text-xl font-bold">Initial Amount Received: {{ $value->received_amount }}</h5>
         <h5 class="text-xl font-bold">Balance: {{ $remainingCredit }}</h5>
         
    </x-filament::section>
    
    <x-filament::section>
        {{ $this->table }}
    </x-filament::section>

</x-filament-panels::page>
