<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnItem extends Model
{
    use HasFactory;
    protected $fillable = [
        "branch_stock_id",
        "branch_id",
        "user_id",
        "quantity_returned",
        "return_date",
        "is_approved",
        "return_note",
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
}
