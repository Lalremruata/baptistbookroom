<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'cost_price',
        'discount',
        'branch_id',
        'item_id',
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
