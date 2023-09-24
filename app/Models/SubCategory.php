<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'subcategory_name',
        'description',
        'category_id',
    ];
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}