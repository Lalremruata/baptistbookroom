<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_id",
        "user_id",
        "item_id",
        "sale_date",
        "quantity",
        "cost_price",
        "selling_price",
        "discount",
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function branch(): BelongsTo{
        return $this->belongsTo(Branch::class);
    }
    public function item(): BelongsTo{
        return $this->belongsTo(Item::class);
    }
}
