<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use App\Http\Requests\Roles\Index as RolesIndex;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RolesController extends Controller
{
    use ApiResponseHelpers;

    public function index(RolesIndex $request): JsonResponse
    {
        $roles = Role::query()
            ->where('name', '!=', 'super_admin')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($roles);
    }
}
