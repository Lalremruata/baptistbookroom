@php
            // Get the item ID
        $itemId = $this->record->item_id;
        // Get the item name
        $itemName = $this->record->item->item_name;

        // Calculate total amount, total sale, and return amount in a more efficient way
        $totalAmount = App\Models\Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
                $query->where('items.id', $itemId);
            })->sum('total_amount');

        $totalSale = App\Models\Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
                $query->where('items.id', $itemId);
            })->sum('quantity');

        $returnAmount = $this->record->privateBookAccounts()->sum('return_amount');

        // Calculate balance
        $balance = $totalAmount - $returnAmount;

    @endphp
<x-filament-panels::page>
<x-filament::section collapsible icon="heroicon-o-book-open"
    icon-color="info" icon-size="lg">
    <x-slot name="heading">
        <h1 class="text-xl">Lehkhabu hming : {{ $itemName}}</h1>
    </x-slot>
        <h5 class="text-xl">Lehkhabu hralh zat: {{ $totalSale }}</h5>
         <h5 class="text-xl">Lehkhabu hralh man : {{ $totalAmount }}</h5>
         <h5 class="text-xl">Balance: {{ $balance }}</h5>
</x-filament::section>

        <x-filament::section >
            {{ $this->table }}
        </x-filament::section>
<x-filament-actions::modals />
</x-filament-panels::page>