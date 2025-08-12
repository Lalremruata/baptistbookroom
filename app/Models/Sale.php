<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sale extends Model
{
    use \Znck\Eloquent\Traits\BelongsToThrough;
    use HasFactory;
    protected $fillable = [
        "branch_stock_id",
        "branch_id",
        "user_id",
        'customer_id',
        "quantity",
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
    public function item()
    {
        return $this->belongsToThrough(Item::class,[MainStock::class, BranchStock::class]);
    }
    public function mainStock()
    {
        return $this->belongsToThrough(MainStock::class,BranchStock::class);
    }
    public function getFormattedInvoiceNumber()
    {
        $currentYear = date('y');
        $nextYear = date('y', strtotime('+1 year'));
        $financialYear = (date('m') > 3) ? $currentYear . '-' . $nextYear : ($currentYear - 1) . '-' . date('y');
        
        $userBranch = $this->branch->branch_name;
        $branchWords = explode(' ', $userBranch);
        
        if (count($branchWords) > 1) {
            $branchCode = substr($branchWords[0], 0, 3) . substr($branchWords[1], 0, 1);
        } else {
            $branchCode = substr($userBranch, 0, 3);
        }
        
        return 'BLS/' . $financialYear . '/' . $branchCode . '/' . $this->memo;
    }
}
