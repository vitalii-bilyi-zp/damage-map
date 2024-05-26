<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DamageNoteRequestPolicy
{
    use HandlesAuthorization;

    public function store(User $user)
    {
        return true;
    }

    public function approveRequest(User $user)
    {
        return false;
    }

    public function declineRequest(User $user)
    {
        return false;
    }
}
