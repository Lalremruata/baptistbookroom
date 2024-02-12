<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;
    protected $fillable = [
        "name","quantity","purchase_date","condition", "branch_id",
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
