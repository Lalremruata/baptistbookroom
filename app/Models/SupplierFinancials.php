<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierFinancials extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'amount_paid', 'balance_after_transaction', 'payment_method'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
