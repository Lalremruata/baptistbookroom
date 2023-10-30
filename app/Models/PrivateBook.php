<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateBook extends Model
{
    use HasFactory;
    protected $fillable = [
        "receive_from",
        "author",
        "file_no",
        "quantity",
        "quantity_return",
    ];
}
