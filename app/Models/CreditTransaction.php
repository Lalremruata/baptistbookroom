<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        "customer_id",
        "recieved_amount",
        "total_amount",
        "recovered_amount",
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
