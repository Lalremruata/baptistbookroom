<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Item extends Model
{
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;
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
    public function sales()
    {
        return $this->hasManyDeepFromRelations($this->branchStocks(), (new BranchStock())->sales());
    }
    public function branchStocks()
    {
        return $this->hasManyThrough(BranchStock::class, MainStock::class );
    }

    public function stockDistribute(): HasMany
    {
        return $this->hasMany(StockDistribute::class);
    }
    public function privateBook(): HasMany
    {
        return $this->hasMany(PrivateBook::class);
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
    public function stockDistributeCart(): BelongsTo
    {
        return $this->belongsTo(StockDistributeCart::class);
    }
}
