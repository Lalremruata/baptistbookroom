<x-filament-panels::page>
    {{--<livewire:SaleFilterComponent />--}}
    <x-filament::section collapsible>
        <x-filament-panels::form wire:submit="showTable">
            {{ $this->form }}
            <x-filament-panels::form.actions
                :actions="$this->getFormActions()"
            />
        </x-filament-panels::form>
    </x-filament::section>
    <x-filament::section>
        {{--@if($sales->count() > 0)
            --}}
        <div class="flex"> 
            @foreach($sales as $sale)
            <div class="inline-block mx-4">
            <table class="w-full border border-collapse">
                <thead>
                    <tr>
                        <th colspan="4" class="p-2 border">{{ $sale->branchStock->branch->branch_name }}</th>
                    </tr>
                    <tr>
                        <th class="p-2 border">Item Name</th>
                        <th class="p-2 border">Quantity Sale</th>
                        <th class="p-2 border">Total Amount</th>
                        <th class="p-2 border">Branch Stock Quantity</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td class="p-2 border">{{ $sale->item->item_name }}</td>
                            <td class="p-2 border">{{ $sale->total_quantity }}</td>
                            <td class="p-2 border">{{ $sale->total_amount }}</td>
                            <td class="p-2 border">{{ $sale->branchStock->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        {{--
        @elseif($itemId)
        <p>No sales found.</p>
        @endif
        --}}
    </x-filament::section>
    <x-filament-actions::modals />
</x-filament-panels::page>


