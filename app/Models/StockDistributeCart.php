<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockDistributeCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'main_stock_id',
        'user_id',
        'quantity',
        'cost_price',
        'mrp',
        'batch',
        'branch_id'
    ];
    public function item(): BelongsTo{
        return $this->belongsTo(Item::class);
    }
    public function mainStock(): BelongsTo
    {
        return $this->belongsTo(MainStock::class);
    }
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    public function branch(): BelongsTo{
        return $this->belongsTo(Branch::class);
    }
}
