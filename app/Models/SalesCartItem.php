<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesCartItem extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
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
        "rate",
        "total_amount",
        "total_amount_with_gst",
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function branch(): BelongsTo{
        return $this->belongsTo(Branch::class);
    }
    public function branchStock(): BelongsTo
    {
        return $this->belongsTo(BranchStock::class);
    }
    public function item()
    {
        return $this->belongsToThrough(Item::class,[MainStock::class, BranchStock::class]);
    }
    public function mainStock()
    {
        return $this->belongsToThrough(MainStock::class,BranchStock::class);
    }
}
