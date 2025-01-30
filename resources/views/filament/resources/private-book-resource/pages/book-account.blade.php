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
         <h5 class="text-xl">Total Return(Lekhabu return zat): {{ $this->totalReturns }}</h5>
         <h5 class="text-xl">Initial Quantity(A tir a lak zat): {{ $this->initialQuantity }}</h5>
         <h5 class="text-xl">All Quantity(main stock + branch stock): {{ $this->totalQuantity }}</h5>
</x-filament::section>

<div x-data="{ tab: 'payment' }">
    <!-- Tab navigation -->
    <div class="border-b border-gray-200">
        <nav class="flex space-x-4" aria-label="Tabs">
            <!-- Payment Tab -->
            <a href="#" 
                @click.prevent="tab = 'payment'"
                :class="{ 'border-indigo-500 text-indigo-600': tab === 'payment', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'payment' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Payment to Author/Submitter
            </a>

            <!-- Returns Tab -->
            <a href="#" 
                @click.prevent="tab = 'returns'"
                :class="{ 'border-indigo-500 text-indigo-600': tab === 'returns', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': tab !== 'returns' }"
                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
            >
                Book Returned to Author/Submitter
            </a>
        </nav>
    </div>

    <!-- Tab content -->
    <div class="mt-4">
        <!-- Payment Tab Content -->
        <div x-show="tab === 'payment'">
            <x-filament::section>
                <livewire:private-book-payment :privateBookId="$record->id" 
                wire:on="paymentUpdated"  {{-- Listen for the event here --}}
                wire:dispatch="refreshQuantities"  {{-- Trigger refresh when the event occurs --}}
                />
            </x-filament::section>
        </div>

        <!-- Returns Tab Content -->
        <div x-show="tab === 'returns'">
            <x-filament::section>
                <livewire:private-book-returns :privateBookId="$record->id" />
            </x-filament::section>
        </div>
    </div>
</div>



<x-filament-actions::modals />
</x-filament-panels::page>
