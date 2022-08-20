<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Regions\Index as RegionsIndex;
use App\Models\Region;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RegionsController extends Controller
{
    use ApiResponseHelpers;

    public function index(RegionsIndex $request): JsonResponse
    {
        $regions = Region::orderByRaw('name COLLATE utf8mb4_unicode_ci')->get();
        return $this->setDefaultSuccessResponse([])->respondWithSuccess($regions);
    }
}
