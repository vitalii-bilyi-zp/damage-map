<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RepairTypes\Index as RepairTypesIndex;
use App\Models\RepairType;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RepairTypesController extends Controller
{
    use ApiResponseHelpers;

    public function index(RepairTypesIndex $request): JsonResponse
    {
        $repairTypes = RepairType::all();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($repairTypes);
    }
}
