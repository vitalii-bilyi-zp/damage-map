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
        $user = auth()->user();

        $communities = Community::query()
            ->select('communities.*')
            ->when(isset($user->region_id), function($query) use (&$user) {
                $query
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->where('districts.region_id', '=', $user->region_id);
            })
            ->when(isset($user->district_id), function($query) use (&$user) {
                $query->where('communities.district_id', '=', $user->district_id);
            })
            ->when(isset($user->community_id), function($query) use (&$user) {
                $query->where('communities.id', '=', $user->community_id);
            })
            ->orderByRaw('communities.name COLLATE utf8mb4_unicode_ci')
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($communities);
    }
}
