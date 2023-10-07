<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockDistribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_id',
    ];
    public function item()
    {
        return $this->hasMany(Item::class);
    }
    public function stockDistributeItem(): HasMany
    {
        return $this->hasMany(StockDistributeItem::class);
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
