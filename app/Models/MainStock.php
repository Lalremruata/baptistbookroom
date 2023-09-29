<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MainStock extends Model
{
    use HasFactory;
    protected $fillable =[
        'quantity',
        'discount',
        'cost_price',
        'item_id',
        'last_update_date',
        'product_id',
    ];
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
