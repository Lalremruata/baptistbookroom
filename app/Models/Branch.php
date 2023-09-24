<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name'
    ];
    public function branchStock(): HasMany
    {
        return $this->HasMany(BranchStock::class);
    }
}
