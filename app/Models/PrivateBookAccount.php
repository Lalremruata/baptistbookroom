<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBookAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        "private_book_id",
        "return_amount",
        "return_date",
    ];
}
