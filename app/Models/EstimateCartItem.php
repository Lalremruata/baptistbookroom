<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateCartItem extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    use HasFactory;

    protected $table = 'estimate_cart_items';

    protected $fillable = [
        "branch_stock_id",
        "item_id",
        "branch_id",
        "user_id",
        "quantity",
        "cost_price",
        "mrp",
        "selling_price",
        "discount",
        "gst_rate",
        "gst_amount",
        "rate",
        "total_amount",
        "total_amount_with_gst",
        'created_at',
    ];

    public $timestamps = true;

    // Cast the custom field as datetime
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // ... other casts
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
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
}
