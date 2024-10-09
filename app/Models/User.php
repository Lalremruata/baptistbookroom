<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'branch_id',
        'address',
        'contact',
    ];
    protected $with = ['roles.permissions'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'user_type' => 'boolean',
    ];
    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function stockDistributeCarts(): HasMany
    {
        return $this->hasMany(StockDistributeCart::class);
    }

    public function hasPermission(string $permission): bool
    {
        $permissonArray = [];

        foreach ($this->roles as $role) {
            foreach ($role->permissions as $singlePermission) {
                $permissonArray[] = $singlePermission->title;
            }
        }

        return collect($permissonArray)->unique()->contains($permission);
    }
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}
