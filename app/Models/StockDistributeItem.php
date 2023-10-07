<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StockDistributeItem extends Pivot
{
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    // public function stockTransfer(): BelongsTo
    // {
    //     return $this->belongsTo(StockTransfer::class);
    // }
}
