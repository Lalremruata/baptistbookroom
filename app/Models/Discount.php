<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable =[
        'discount_percentage',
    ];
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
