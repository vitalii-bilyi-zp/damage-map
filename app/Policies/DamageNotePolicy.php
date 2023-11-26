<?php

namespace App\Policies;

use App\Models\User;
use App\Models\DamageNote;
use App\Models\Community;
use Illuminate\Auth\Access\HandlesAuthorization;

class DamageNotePolicy
{
    use HandlesAuthorization;

    public function viewList(User $user)
    {
        return true;
    }

    public function store(User $user)
    {
        if ($user->isAnalyst()) {
            return false;
        }

        if (isset($user->community_id)) {
            return request()->get('community_id') === $user->community_id;
        }
        if (isset($user->district_id)) {
            return Community::query()
                ->where('communities.id', request()->get('community_id'))
                ->where('communities.district_id', $user->district_id)
                ->exists();
        }
        if (isset($user->region_id)) {
            return Community::query()
                ->join('districts', 'communities.district_id', '=', 'districts.id')
                ->where('communities.id', request()->get('community_id'))
                ->where('districts.region_id', $user->region_id)
                ->exists();
        }

        return true;
    }

    public function storeFromFile(User $user)
    {
        return false;
    }

    public function exportCsv(User $user)
    {
        return true;
    }

    public function view(User $user, DamageNote $damageNote)
    {
        if ($user->isAnalyst()) {
            return true;
        }

        if (isset($user->community_id)) {
            return $damageNote->community_id === $user->community_id;
        }
        if (isset($user->district_id)) {
            return Community::query()
                ->where('communities.id', $damageNote->community_id)
                ->where('communities.district_id', $user->district_id)
                ->exists();
        }
        if (isset($user->region_id)) {
            return Community::query()
                ->join('districts', 'communities.district_id', '=', 'districts.id')
                ->where('communities.id', $damageNote->community_id)
                ->where('districts.region_id', $user->region_id)
                ->exists();
        }

        return true;
    }

    public function update(User $user, DamageNote $damageNote)
    {
        if ($user->isAnalyst()) {
            return false;
        }

        if (isset($user->community_id)) {
            return $damageNote->community_id === $user->community_id;
        }
        if (isset($user->district_id)) {
            return Community::query()
                ->where('communities.id', $damageNote->community_id)
                ->where('communities.district_id', $user->district_id)
                ->exists();
        }
        if (isset($user->region_id)) {
            return Community::query()
                ->join('districts', 'communities.district_id', '=', 'districts.id')
                ->where('communities.id', $damageNote->community_id)
                ->where('districts.region_id', $user->region_id)
                ->exists();
        }

        return true;
    }

    public function destroy(User $user, DamageNote $damageNote)
    {
        if ($user->isAnalyst()) {
            return false;
        }

        if (isset($user->community_id)) {
            return $damageNote->community_id === $user->community_id;
        }
        if (isset($user->district_id)) {
            return Community::query()
                ->where('communities.id', $damageNote->community_id)
                ->where('communities.district_id', $user->district_id)
                ->exists();
        }
        if (isset($user->region_id)) {
            return Community::query()
                ->join('districts', 'communities.district_id', '=', 'districts.id')
                ->where('communities.id', $damageNote->community_id)
                ->where('districts.region_id', $user->region_id)
                ->exists();
        }

        return true;
    }
}
