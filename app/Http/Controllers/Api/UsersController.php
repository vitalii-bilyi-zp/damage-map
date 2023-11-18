<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Users\Store as UsersStore;
use App\Http\Requests\Users\Update as UsersUpdate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
{
    use ApiResponseHelpers;

    public function index(): JsonResponse
    {
        $users = User::query()
            ->where('id', '!=', auth()->user()->id)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($users);
    }

    public function store(UsersStore $request) {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'email_verified_at' => now(),
            'password' => Hash::make($request->password),
            'api_token' => uniqid(Str::random(60)),
        ]);

        $user->assignRole($request->role);

        return $this->respondWithSuccess();
    }

    public function show(User $user) {
        $response = [
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first()
        ];

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($response);
    }

    public function update(UsersUpdate $request, User $user) {
        $user->update([
            'name' => $request->name ?? $user->name,
            'password' => $request->new_password ? Hash::make($request->new_password) : $user->password,
        ]);

        $currentRoles = $user->getRoleNames();
        if ($request->get('role') && !$currentRoles->contains($request->role)) {
            foreach ($currentRoles as $role) {
                $user->removeRole($role);
            }

            $user->assignRole($request->role);
        }

        return $this->respondWithSuccess();
    }

    public function showRoles(): JsonResponse
    {
        $roles = Role::query()
            ->where('name', '!=', 'super_admin')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($roles);
    }
}
