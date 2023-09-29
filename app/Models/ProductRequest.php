<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity_requested',
        'request_date',
        'status',
        'product_id',
        'branch_id'
    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
