<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentType extends Model
{
    use HasFactory;
    protected $fillable = [
        'type'
    ];
    protected $casts = [
        'type' => Type::class
    ];

    public function supplierFinancials(): HasMany
    {
        return $this->hasMany(SupplierFinancials::class);
    }
}
