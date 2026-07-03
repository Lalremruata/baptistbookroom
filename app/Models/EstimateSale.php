<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EstimateSale extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    use HasFactory;

    protected $table = 'estimate_sales';

    protected $fillable = [
        "branch_stock_id",
        "item_id",
        "branch_id",
        "user_id",
        'customer_id',
        "quantity",
        "mrp",
        "discount",
        "total_amount",
        'gst_rate',
        'gst_amount',
        'rate',
        'total_amount_with_gst',
        'payment_mode',
        'transaction_number',
        'memo',
        'created_at',
    ];

    public $timestamps = true;

    // Cast the custom field as datetime
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        // ... other casts
    ];
    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
    public function branch(): BelongsTo{
        return $this->belongsTo(Branch::class);
    }
    public function branchStock(): BelongsTo
    {
        return $this->belongsTo(BranchStock::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    public function getFormattedInvoiceNumber()
    {
        // Financial year hardcoded per request (revisit each new FY)
        $financialYear = '25-26';

        $userBranch = $this->branch->branch_name ?? '';
        $branchWords = explode(' ', $userBranch);

        if (count($branchWords) > 1) {
            $branchCode = substr($branchWords[0], 0, 3) . substr($branchWords[1], 0, 1);
        } else {
            $branchCode = substr($userBranch, 0, 3);
        }

        return 'BLS/' . $financialYear . '/' . $branchCode . '/' . str_pad($this->memo, 4, '0', STR_PAD_LEFT);
    }
}
