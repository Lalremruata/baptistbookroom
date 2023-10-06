<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'transfer_date',
        'notes',
        'item_id',
        'branch_id',
    ];
    public function item()
    {
        return $this->hasMany(Item::class);
    }
    public function itemStockTransfer(): HasMany
    {
        return $this->hasMany(ItemStockTransfer::class);
    }
    // public function stockTransferMainStock(): HasMany
    // {
    //     return $this->hasMany(StockTransferMainStock::class);
    // }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
