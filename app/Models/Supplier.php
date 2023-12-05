<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_name',
        'contact_person',
        'contact_number',
        'email',
        'address',
        'city',
        'state',
        'zip_code',
        'country',
        'supplier_type',
        'payment_terms',
        'account_number',
        'initial_balance',
        'current_balance',
        'notes',
    ];

    public function financials()
    {
        return $this->hasMany(SupplierFinancials::class);
    }
    public function getRemainingBalanceAttribute()
    {
        return $this->current_balance;
    }
}
