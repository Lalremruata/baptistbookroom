<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockDistribute extends Model
{
    use HasFactory;
    protected $fillable = [
        'main_stock_id','branch_id', 'quantity','cost_price','mrp','batch',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function mainStock()
    {
        return $this->belongsTo(MainStock::class);
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
