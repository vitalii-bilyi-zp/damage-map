<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\DamageNotes\Store as DamageNotesStore;
use App\Http\Requests\DamageNotes\ShowRegions as DamageNotesShowRegions;
use App\Http\Requests\DamageNotes\ShowDistricts as DamageNotesShowDistricts;
use App\Http\Requests\DamageNotes\ShowCommunities as DamageNotesShowCommunities;
use App\Models\DamageNote;
use Illuminate\Support\Facades\DB;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class DamageNotesController extends Controller
{
    use ApiResponseHelpers;

    public function store(DamageNotesStore $request): JsonResponse
    {
        DamageNote::create([
            'object_type_id' => $request->object_type_id,
            'community_id' => $request->community_id,
            'city' => $request->city,
            'street'  => $request->street,
            'building_number' => $request->building_number,
            'damage_type' => $request->damage_type,
            'restoration_cost' => $request->restoration_cost
        ]);

        return $this->respondWithSuccess();
    }

    public function showRegions(DamageNotesShowRegions $request): JsonResponse
    {
        $aggregation = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('districts', 'communities.district_id', '=', 'districts.id')
            ->join('regions', 'districts.region_id', '=', 'regions.id')
            ->groupBy('regions.id')
            ->select(DB::raw('regions.name, SUM(damage_notes.restoration_cost) AS restoration_cost'))
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    public function showDistricts(DamageNotesShowDistricts $request): JsonResponse
    {
        $aggregation = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->join('districts', 'communities.district_id', '=', 'districts.id')
            ->groupBy('districts.id')
            ->select(DB::raw('districts.name, SUM(damage_notes.restoration_cost) AS restoration_cost'))
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }

    public function showCommunities(DamageNotesShowCommunities $request): JsonResponse
    {
        $aggregation = DamageNote::query()
            ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
            ->groupBy('communities.id')
            ->select(DB::raw('communities.name, SUM(damage_notes.restoration_cost) AS restoration_cost'))
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($aggregation);
    }
}
