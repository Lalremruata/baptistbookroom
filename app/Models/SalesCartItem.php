<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCartItem extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_id",
        "item_id",
        "quantity",
        "cost_price",
        "selling_price",
        "discount",
    ];
}
