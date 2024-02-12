<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_stock_id",
        "user_id",
        "item_id",
        'customer_id',
        "quantity",
        "discount",
        "total_amount",
        'payment_mode',
        'transaction_number',
        'memo',
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function branch(): BelongsTo{
        return $this->belongsTo(Branch::class);
    }
    public function branchStock(): BelongsTo{
        return $this->belongsTo(BranchStock::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
