<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ObjectTypes\Index as ObjectTypesIndex;
use App\Models\ObjectCategory;
use App\Models\ObjectType;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class ObjectTypesController extends Controller
{
    use ApiResponseHelpers;

    public function index(ObjectTypesIndex $request): JsonResponse
    {
        $objectTypes = ObjectType::all();
        $objectTypes->transform(function ($item, $key) {
            if (isset($item->object_category_id)) {
                $item->object_category = ObjectCategory::findOrFail($item->object_category_id);
            }

            return $item;
        });

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($objectTypes);
    }
}
