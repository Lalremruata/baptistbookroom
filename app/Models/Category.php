<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;
    protected $fillable =[
        'category_name',
        'description',
    ];
    public function subcategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}
