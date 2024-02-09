<?php

namespace App\Policies;

use App\Models\ReturnItem;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReturnItemPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('return_item_access');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ReturnItem $returnItem): bool
    {
        return $user->hasPermission('return_item_show');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('return_item_create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ReturnItem $returnItem): bool
    {
        return $user->hasPermission('return_item_edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ReturnItem $returnItem): bool
    {
        return $user->hasPermission('return_item_delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ReturnItem $returnItem)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ReturnItem $returnItem): bool
    {
        return $user->hasPermission('return_item_delete');
    }
}
