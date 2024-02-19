<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \Znck\Eloquent\Traits\BelongsToThrough;

class BranchStock extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'cost_price',
        'mrp',
        'batch',
        'barcode',
        'discount',
        'branch_id',
        'main_stock_id',
    ];
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    public function mainStock(): BelongsTo
    {
        return $this->belongsTo(MainStock::class);
    }
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
    public function item(): BelongsToThrough
    {
        return $this->belongsToThrough(Item::class,MainStock::class);
    }
}
