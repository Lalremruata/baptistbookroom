<div>
@php
        use App\Models\Sale;

        $itemId = $getRecord()->item_id;
        $totalAmount =Sale::whereHas('branchStock.mainStock.item', function ($query) use ($itemId) {
            $query->where('items.id', $itemId);
        })
        ->sum('total_amount');
    @endphp
    {{$totalAmount}}  
</div>
