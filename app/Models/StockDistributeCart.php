<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockDistributeCart extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'user_id',
        'quantity'
    ];
    public function item(): BelongsTo{
        return $this->belongsTo(Item::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
