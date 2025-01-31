<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_stock_id",
        "branch_id",
        "user_id",
        "quantity",
        "cost_price",
        "selling_price",
        "discount",
        "gst_rate",
        "gst_amount",
        "total_amount",
        "total_amount_with_gst",
    ];
    public function branchStock(): BelongsTo{
        return $this->belongsTo(BranchStock::class);
    }
}
