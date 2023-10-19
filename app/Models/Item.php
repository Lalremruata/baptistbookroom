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
        'category_id',
        'sub_category_id',
        'item_name',
        'barcode',
        'description',
    ];

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
    public function stockDistribute(): HasMany
    {
        return $this->hasMany(StockDistribute::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function cartItems(): BelongsTo
    {
        return $this->belongsTo(CartItem::class);
    }
}
