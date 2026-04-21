<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RepairTypes\Index as RepairTypesIndex;
use App\Models\DamageNote;
use App\Models\RepairType;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RepairTypesController extends Controller
{
    use ApiResponseHelpers;

    public function index(RepairTypesIndex $request): JsonResponse
    {
        $query = RepairType::query();

        $heritageStatus = $request->input('heritage_status');
        if ($heritageStatus && array_key_exists($heritageStatus, DamageNote::HERITAGE_ALLOWED_REPAIR_CODES)) {
            $allowedCodes = DamageNote::HERITAGE_ALLOWED_REPAIR_CODES[$heritageStatus];
            if ($allowedCodes !== null) {
                $query->whereIn('code', $allowedCodes);
            }
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($query->get());
    }
}
