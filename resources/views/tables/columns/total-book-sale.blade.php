<div>
    @php
        use App\Models\Sale;

        $itemId = $getRecord()->item_id;
        $totalSale =Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })
        ->sum('quantity');
    @endphp
    {{$totalSale}}   
</div>
