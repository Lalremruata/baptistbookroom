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
        'opening_balance',
        'account_number',
        'ifsc_code',
        'bank_name',
        'branch',
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
