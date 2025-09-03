<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateBookAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        "private_book_id",
        "return_amount",
        "quantity",
        "return_date",
        "receiver_name",
        "address",
        "pin_code",
        "phone_number",
        "payment_mode",
        "transaction_number",
        "account_number",
        "account_holder",
        "branch_name",
        "ifsc_code",
        "notes",
    ];
    public function privateBook(): BelongsTo
    {
        return $this->belongsTo(PrivateBook::class);
    }
}
