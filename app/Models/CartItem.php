<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'item_id',
        'user_id',
        'quantity'
    ];
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
