<div>
    <form wire:submit.prevent="applyFilters" class="mr-4">
        <div class="mb-4">
            <label class="block mb-2" for="subCategory">Select Sub Category:</label>
            <select wire:model="subCategoryId" id="subCategory" class="p-2 border rounded" wire:change="updateItems">
                <option value="">All Sub Categories</option>
                @foreach($subCategories as $subCategory)
                    <option value="{{ $subCategory->id }}">{{ $subCategory->subcategory_name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-2" for="item">Select Item:</label>
            <select wire:model="itemId" id="item" class="p-2 border rounded">
                <option value="">All Items</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}">{{ $item->item_name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="p-2 bg-blue-500 text-white rounded"
         wire:disabled="!$itemId">Apply Filters</button>
    </form>

    <x-filament::section>
        @if($itemId && $sales->count() > 0)
        <div class="flex">
            <div class="inline-block">
            <table class="w-full border border-collapse">
                <thead>
                    @foreach($sales as $sale)
                    <tr>
                        <th colspan="4" class="p-2 border">{{ $sale->branch->branch_name }}</th>
                    </tr>
                    <tr>
                        <th class="p-2 border">Quantity</th>
                        <th class="p-2 border">Total Amount</th>
                        <th class="p-2 border">Branch Stock Quantity</th>
                    </tr>
                </thead>
                <tbody>
                        <tr>
                            <td class="p-2 border">{{ $sale->quantity }}</td>
                            <td class="p-2 border">{{ $sale->total_amount }}</td>
                            <!-- Replace with the actual branch stock data -->
                            <td class="p-2 border">{{ $sale->branchStock->quantity }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
        </div>
        @elseif($itemId)
        <p>No sales found.</p>
        @endif
    </x-filament::section>

</div>
