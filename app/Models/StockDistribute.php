<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockDistribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id','branch_id', 'quantity'
    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
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
