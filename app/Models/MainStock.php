<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MainStock extends Model
{
    use HasFactory;
    protected $fillable =[
        'item_id',
        'cost_price',
        'mrp',
        'batch',
        'quantity',
        'barcode',
    ];
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    public function stockDistribute()
    {
        return $this->hasMany(StockDistribute::class);
    }
    public function branchStock()
    {
        return $this->hasMany(BranchStock::class);
    }
    public function privateBook(): HasMany
    {
        return $this->hasMany(PrivateBook::class);
    }
}
