<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_name',
        'description',
        'price',
        'category_id',
        'sub_category_id',
    ];
    public function itemStockTransfer(): HasMany
    {
        return $this->hasMany(ItemStockTransfer::class);
    }
    public function mainStock(): HasMany
    {
        return $this->hasMany(MainStock::class);
    }
    public function branchStock(): HasMany
    {
        return $this->hasMany(BranchStock::class);
    }
    public function productRequests(): HasMany
    {
        return $this->hasMany(ProductRequest::class);
    }
    public function stockTransfer(): HasMany
    {
        return $this->hasMany(StockTransfer::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
