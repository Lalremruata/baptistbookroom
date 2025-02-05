<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'branch_name',
        'branch_address',
        'branch_phone',
        'branch_email',
    ];
    public function branchStock(): HasMany
    {
        return $this->HasMany(BranchStock::class);
    }
    public function stockDistributeCart(): HasMany
    {
        return $this->hasMany(StockDistributeCart::class);
    }
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    public function asset(): HasMany
    {
        return $this->hasMany(Asset::class);
    }
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

}
