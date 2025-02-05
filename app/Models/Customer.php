<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'phone',
        'sales_id',
        'address'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
    public function creditTransaction()
    {
        return $this->hasMany(CreditTransaction::class);
    }
}
