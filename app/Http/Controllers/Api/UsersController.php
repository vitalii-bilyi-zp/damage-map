<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Requests\Users\Index as UsersIndex;
use App\Http\Requests\Users\Store as UsersStore;
use App\Http\Requests\Users\Show as UsersShow;
use App\Http\Requests\Users\Update as UsersUpdate;
use App\Http\Requests\Users\Destroy as UsersDestroy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use ApiResponseHelpers;

    public function index(UsersIndex $request): JsonResponse
    {
        $users = User::query()
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'regions.name AS region',
                'districts.name AS district',
                'communities.name AS community',
            ])
            ->leftJoin('communities', 'users.community_id', '=', 'communities.id')
            ->leftJoin('districts', 'users.district_id', '=', 'districts.id')
            ->leftJoin('regions', 'users.region_id', '=', 'regions.id')
            ->where('users.id', '!=', auth()->user()->id)
            ->orderBy('users.created_at', 'DESC')
            ->get();

        $users->transform(function ($item, $key) {
            $item->roles = $item->roles()->pluck('display_name')->join(', ');

            return $item;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($users);
    }

    public function store(UsersStore $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'api_token' => uniqid(Str::random(60)),
            'region_id' => $request->region ?? null,
            'district_id' => $request->district ?? null,
            'community_id' => $request->community ?? null,
        ]);

        $user->assignRole($request->role);

        return $this->respondWithSuccess();
    }

    public function show(UsersShow $request, User $user) {
        $response = [
            'name' => $user->name,
            'email' => $user->email,
        ];

        if (auth()->user()->isSuperAdmin()) {
            $response['role'] = $user->getRoleNames()->first();
            $response['region_id'] = $user->region_id;
            $response['district_id'] = $user->district_id;
            $response['community_id'] = $user->community_id;
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($response);
    }

    public function update(UsersUpdate $request, User $user) {
        $dataForUpdate = [
            'name' => $request->name ?? $user->name,
        ];

        if (auth()->user()->id === $user->id) {
            $dataForUpdate['password'] = $request->new_password ? Hash::make($request->new_password) : $user->password;
        } else if (auth()->user()->isSuperAdmin()) {
            $dataForUpdate['region_id'] = $request->region ?? null;
            $dataForUpdate['district_id'] = $request->district ?? null;
            $dataForUpdate['community_id'] = $request->community ?? null;

            $currentRoles = $user->getRoleNames();
            if ($request->get('role') && !$currentRoles->contains($request->role)) {
                foreach ($currentRoles as $role) {
                    $user->removeRole($role);
                }

                $user->assignRole($request->role);
            }
        }

        $user->update($dataForUpdate);

        return $this->respondWithSuccess();
    }

    public function destroy(UsersDestroy $request, User $user)
    {
        $user->delete();

        return $this->respondWithSuccess();
    }
}
