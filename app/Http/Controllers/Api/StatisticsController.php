<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\ShowRatio as StatisticsShowRatio;
use App\Models\DamageNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    use ApiResponseHelpers;

    public function showRatio(StatisticsShowRatio $request): JsonResponse
    {
        $aggregation = DamageNote::query()
            ->whereDate('date', '>=', $request->start_date)
            ->whereDate('date', '<=', $request->end_date)
            ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
            ->when($request->get('object_category_id'), function(Builder $query) use (&$request) {
                $query->where('object_types.object_category_id', '=', $request->get('object_category_id'));
            })
            ->when($request->get('region_id'), function(Builder $query) use (&$request) {
                $query
                    ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->join('regions', 'districts.region_id', '=', 'regions.id')
                    ->where('regions.id', '=', $request->get('region_id'));
            })
            ->groupBy('damage_notes.object_type_id')
            ->select(DB::raw('object_types.name, SUM(damage_notes.restoration_cost) AS restoration_cost'))
            ->get();

        $data = $aggregation->reduce(function ($carry, $item) {
            $carry[$item->name] = $item->restoration_cost;
            return $carry;
        }, []);

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($data);
    }
}
