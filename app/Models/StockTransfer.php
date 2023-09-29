<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransfer extends Model
{
    use HasFactory;
    protected $fillable = [
        'quantity',
        'transfer_date',
        'notes',
        'item_id',
        'branch_id',
    ];
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
