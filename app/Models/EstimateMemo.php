<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateMemo extends Model
{
    use HasFactory;

    protected $table = 'estimate_memos';

    protected $fillable = [
        "memo",
        "branch_id",
    ];
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
