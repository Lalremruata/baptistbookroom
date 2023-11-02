<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_id",
        "user_id",
        "item_id",
        "quantity",
        "cost_price",
        "selling_price",
        "discount",
    ];
    public function branchStock(): BelongsTo{
        return $this->belongsTo(BranchStock::class);
    }
}
