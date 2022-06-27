<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Communities\Index as CommunitiesIndex;
use App\Models\Community;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class CommunitiesController extends Controller
{
    use ApiResponseHelpers;

    public function index(CommunitiesIndex $request): JsonResponse
    {
        $communities = Community::orderByRaw('name COLLATE utf8mb4_unicode_ci')->get();
        return $this->setDefaultSuccessResponse([])->respondWithSuccess($communities);
    }
}
