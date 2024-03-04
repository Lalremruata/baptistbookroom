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

        <div class="flex">
            <div class="inline-block mx-4">

            <table class="w-full border border-collapse">

                <thead>
                    <tr>
                        <th  class="p-2 border">Item Name</th>
                        @foreach ($branches as $branch)
                        <th colspan="3" class="p-2 border">{{ $branch->branch_name }}</th>
                        @endforeach
                    </tr>
                </thead>
            <tbody>
                <tr>
                    <th class="p-2 border">{{ $itemName }}</th>
                    @foreach ($branches as $branch)
                        <th class="p-2 border">Quantity Sale</th>
                        <th class="p-2 border">Total Amount</th>
                        <th class="p-2 border">Branch Stock Quantity</th>
                    @endforeach
                </tr>
                <tr>
                    <th class="p-2 border"></th>
                    @foreach ($branches as $branch)
                        <td class="p-2 border">{{ $sales->total_quantity }}</td>
                        <td class="p-2 border">{{ $sale->total_amount }}</td>
                        <td class="p-2 border">{{ $sale->branchStock->quantity }}</td>
                    @endforeach
                </tr>
            </tbody>


                    {{-- @foreach($sales as $sale)
                    <tr>
                        <th rowspan="3" class="p-2 border">{{ $sale->item->item_name }}</th>
                    </tr>
                    <tr>
                        <th class="p-2 border">Quantity Sale</th>
                        <th class="p-2 border">Total Amount</th>
                        <th class="p-2 border">Branch Stock Quantity</th>
                    </tr>
                </thead>
                <tbody>

                        <tr>
                            <td class="p-2 border">{{ $sale->total_quantity }}</td>
                            <td class="p-2 border">{{ $sale->total_amount }}</td>
                            <td class="p-2 border">{{ $sale->branchStock->quantity }}</td>
                        </tr>

                </tbody>
                @endforeach --}}
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


