<x-filament-panels::page>
    {{--<livewire:SaleFilterComponent />--}}
    <x-filament::section collapsible>
        <x-filament-panels::form wire:submit="showTable">

            {{ $this->form }}
            {{--
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
            --}}
        </x-filament-panels::form>
    </x-filament::section>
    <x-filament::section>
        {{--@if($sales->count() > 0)
            --}}

        <div class="flex overflow-auto">
            <div class="inline-block mx-4">
@if($itemName)
<table class="w-full border border-collapse">
    <thead>
        <tr>
            <th class="p-2 border">Item Name</th>
            @foreach ($branches as $branch)
                <th colspan="4" class="p-2 border">{{ $branch->branch_name }}</th>
            @endforeach
            <th colspan="4" class="p-2 border">Summary</th>
            <th class="p-2 border"></th>
            <th class="p-2 border"></th>
        </tr>
    </thead>
    <tbody>
        <!-- First Row: Column Labels for Each Branch -->
        <tr>
            <th rowspan="3" class="p-2 border">{{ $itemName }}</th>
            @foreach ($branches as $branch)
                <th class="p-2 border">Quantity Sale</th>
                <th class="p-2 border">Total Amount</th>
                <th class="p-2 border">Branch Stock Quantity</th>
                <th class="p-2 border">Transferred Quantity</th>
            @endforeach
            <!-- Column labels for the Summary, Main Stock, and All Stock -->
            <th class="p-2 border">Total Quantity Sale</th>
            <th class="p-2 border">Total Amount</th>
            <th class="p-2 border">Branch Stock Quantity</th>
            <th class="p-2 border">Total Transferred Quantity</th>
            <th class="p-2 border">Main Stock Quantity</th>
            <th class="p-2 border">All Stock (Branch + Main)</th>
        </tr>

        <!-- Second Row: Data for Each Branch -->
        <tr>
            @foreach ($branches as $branch)
                @php
                    $sales1 = $sales->where('branch_id', $branch->id);
                    $saleQty = $sales1->sum('quantity');
                    $saleAmt = $sales1->sum('total_amount');
                    $branchStockQty = $branchStock->where('branch_id', $branch->id)->sum('quantity');
                @endphp

                <td class="p-2 border">{{ $saleQty }}</td>
                <td class="p-2 border">{{ $saleAmt }}</td>
                <td class="p-2 border">{{ $branchStockQty }}</td>
                <td class="p-2 border">{{ $branchStockQty + $saleQty }}</td>
            @endforeach

            <!-- Summary Columns -->
            <td class="p-2 border">{{ $sales->sum('quantity') }}</td>
            <td class="p-2 border">{{ $sales->sum('total_amount') }}</td>
            <td class="p-2 border">{{ $branchStock->sum('quantity') }}</td>
            <td class="p-2 border">{{ $branchStock->sum('quantity') + $sales->sum('quantity') }}</td>

            <!-- Main Stock and All Stock -->
            <td class="p-2 text-center">{{ $mainStock ?? 0 }}</td>
            <td class="p-2 border">{{ $branchStock->sum('quantity') + $mainStock }}</td>
        </tr>
    </tbody>
</table>

            @endif
            </div>
        </div>

    </x-filament::section>
    <x-filament-actions::modals />
</x-filament-panels::page>


