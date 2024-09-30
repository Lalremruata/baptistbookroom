<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateBookReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        "private_book_id",
        "return_amount",
        "return_date",
        "receiver_name",
        "address",
        "phone_number",
    ];
    public function privateBook(): BelongsTo
    {
        return $this->belongsTo(PrivateBook::class);
    }
}
