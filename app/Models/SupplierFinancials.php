<?php

namespace App\Models;

use App\Enums\Type;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierFinancials extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'type',
        'voucher_no',
        'amount',
        'payment_mode',
        'transaction_number',
        'remarks'
    ];
    protected $casts = [
        'type' => Type::class
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function type(): BelongsTo
    {
        return $this->belongsTo(PaymentType::class);
    }
}
