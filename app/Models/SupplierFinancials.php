<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierFinancials extends Model
{
    use HasFactory;
    protected $fillable = ['supplier_id', 'bill_no', 'credit', 'debit', 'balance','remarks'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
