<?php

namespace App\Policies;

use App\Models\BranchStock;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BranchStockPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('branch_stock_access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BranchStock $branchStock): bool
    {
        return $user->hasPermission('branch_stock_show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('branch_stock_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BranchStock $branchStock): bool
    {
        return $user->hasPermission('branch_stock_edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BranchStock $branchStock): bool
    {
        return $user->hasPermission('branch_stock_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BranchStock $branchStock)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BranchStock $branchStock)
    {
        //
    }
}
