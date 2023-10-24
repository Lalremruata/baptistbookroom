<?php

namespace App\Policies;

use App\Models\MainStock;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class MainStockPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('main_stock_access');

    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MainStock $mainStock): bool
    {
        return $user->hasPermission('main_stock_show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('main_stock_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MainStock $mainStock): bool
    {
        return $user->hasPermission('main_stock_edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MainStock $mainStock): bool
    {
        return $user->hasPermission('main_stock_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, MainStock $mainStock)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function deleteAny(User $user)
    {
        return $user->hasPermission('main_stock_delete');
    }
}
