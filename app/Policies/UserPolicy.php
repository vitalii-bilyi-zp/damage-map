<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function viewList(User $user)
    {
        return $user->hasPermissionTo('users.viewList');
    }

    public function store(User $user)
    {
        return $user->hasPermissionTo('users.store');
    }

    public function view(User $current, User $user)
    {
        return $current->hasPermissionTo('users.view') && $current->id === $user->id;
    }

    public function update(User $current, User $user)
    {
        return $current->hasPermissionTo('users.update') && $current->id === $user->id;
    }

    public function destroy(User $user)
    {
        return $user->hasPermissionTo('users.destroy');
    }
}
