@php
            // Get the item ID
        $itemId = $this->record->item_id;
        // Get the item name
        $itemName = $this->record->item->item_name;

        // Calculate total amount, total sale, and return amount
        $totalAmount = App\Models\Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
                $query->where('items.id', $itemId);
            })->sum('total_amount');

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
        <h5 class="text-xl">Lehkhabu hralh zat: {{ $this->totalSale }}</h5>
         <h5 class="text-xl">Lehkhabu hralh man : {{ $totalAmount }}</h5>
         <h5 class="text-xl">Balance: {{ $balance }}</h5>
         <h5 class="text-xl">Initial Quantity: {{ $this->totalQuantity }}</h5>
</x-filament::section>


        <div class="lg:flex">
        <x-filament::section class="w-full lg:w-1/2 p-4">
            <div >
                <h2 class="text-lg font-semibold mb-4">Payment to Author/Submitter</h2>
                <livewire:private-book-payment :privateBookId="$record->id" />
            </div>
        </x-filament::section>
        <x-filament::section class="w-full lg:w-1/2 p-4">
        <div>
                <h2 class="text-lg font-semibold mb-4">Book Returned to Author/Submitter</h2>
                <livewire:private-book-returns :privateBookId="$record->id" />
            </div>
        </x-filament::section>
        </div>

<x-filament-actions::modals />
</x-filament-panels::page>
