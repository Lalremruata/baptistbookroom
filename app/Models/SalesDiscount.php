<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesDiscount extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_id',
        'discount_id',
        'discounted_amount',
    ];
    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
