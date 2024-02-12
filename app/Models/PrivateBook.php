<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateBook extends Model
{
    use HasFactory;
    protected $fillable = [
        "item_id",
        "main_stock_id",
        "receive_from",
        "author",
        "file_no",
        "quantity",
    ];
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }
    public function mainStock(): BelongsTo
    {
        return $this->belongsTo(MainStock::class);
    }

}
