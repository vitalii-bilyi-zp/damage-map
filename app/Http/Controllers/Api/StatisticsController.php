<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\ShowGlobal as StatisticsShowGlobal;
use App\Http\Requests\Statistics\ShowRatio as StatisticsShowRatio;
use App\Models\DamageNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonInterface;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class StatisticsController extends Controller
{
    use ApiResponseHelpers;

    const DAILY_PERIOD = 0;
    const WEEKLY_PERIOD = 1;
    const MONTHLY_PERIOD = 2;

    const PERIODS = [
        self::DAILY_PERIOD,
        self::WEEKLY_PERIOD,
        self::MONTHLY_PERIOD,
    ];

    const PERIODS_WORD_MAP = [
        self::DAILY_PERIOD => 'day',
        self::WEEKLY_PERIOD => 'week',
        self::MONTHLY_PERIOD => 'month'
    ];

    protected function getWeekTitle(CarbonInterface $date): string
    {
        $startDate = $date->copy()->startOfWeek();
        $endDate = $date->copy()->endOfWeek();
        $startMonth = $startDate->translatedFormat('F');
        $endMonth = $endDate->translatedFormat('F');
        $suffixMonth = $startMonth === $endMonth ? '' : $endMonth;
        $startYear = $startDate->translatedFormat('Y');
        $endYear = $endDate->translatedFormat('Y');
        $suffixYear = $startYear === $endYear ? '' : $endYear;
        return $suffixYear ? "{$startMonth} {$startDate->translatedFormat('d')} {$startYear}-{$suffixMonth} {$endDate->translatedFormat('d')}, {$suffixYear}" :
            "{$startMonth} {$startDate->translatedFormat('d')}-{$suffixMonth} {$endDate->translatedFormat('d')}, {$startYear}";
    }

    public function showGlobal(StatisticsShowGlobal $request): JsonResponse
    {
        $startDate = Carbon::parse($request->get('start_date'));
        $endDate = Carbon::parse($request->get('end_date'));
        $periodType = $request->get('period_type');

        $query = DamageNote::query()
            ->whereDate('damage_notes.date', '>=', $request->start_date)
            ->whereDate('damage_notes.date', '<=', $request->end_date)
            ->when($request->get('region_id'), function(Builder $query) use (&$request) {
                $query
                    ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->join('regions', 'districts.region_id', '=', 'regions.id')
                    ->where('regions.id', '=', $request->get('region_id'));
            });

        switch($periodType) {
            case self::MONTHLY_PERIOD:
                $query->groupBy(DB::raw("DATE_FORMAT(damage_notes.date, '%Y-%m')"));
                break;
            case self::WEEKLY_PERIOD:
                $query->groupBy(DB::raw("YEAR(damage_notes.date)"), DB::raw("WEEKOFYEAR(damage_notes.date)"));
                break;
            default:
                $query->groupBy(DB::raw("DATE(damage_notes.date)"));
                break;
        }

        $aggregation = $query
            ->select(DB::raw('MAX(damage_notes.date) AS max_date, SUM(damage_notes.restoration_cost) AS restoration_cost'))
            ->get();

        $preparedData = [];
        switch($periodType) {
            case self::MONTHLY_PERIOD:
                $dates = (new CarbonPeriod($startDate->startOfMonth(), "1 " . self::PERIODS_WORD_MAP[$periodType], $endDate->endOfMonth()))->toArray();
                for ($i = 0; $i < count($dates); $i++) {
                    $currentDate = $dates[$i];
                    $key = $currentDate->copy()->translatedFormat('F, Y');
                    $index = $aggregation->search(function ($item) use ($currentDate) {
                        return Carbon::parse($item->max_date)->between($currentDate->copy()->startOfMonth(), $currentDate->copy()->endOfMonth());
                    });
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->restoration_cost : null;
                }
                break;
            case self::WEEKLY_PERIOD:
                $dates = (new CarbonPeriod($startDate->startOfWeek(), "1 " . self::PERIODS_WORD_MAP[$periodType], $endDate->endOfWeek()))->toArray();
                for ($i = 0; $i < count($dates); $i++) {
                    $currentDate = $dates[$i];
                    $key = $this->getWeekTitle($currentDate);
                    $index = $aggregation->search(function ($item) use ($currentDate) {
                        return Carbon::parse($item->max_date)->between($currentDate->copy()->startOfWeek(), $currentDate->copy()->endOfWeek());
                    });
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->restoration_cost : null;
                }
                break;
            default:
                $dates = (new CarbonPeriod($startDate, "1 " . self::PERIODS_WORD_MAP[$periodType], $endDate))->toArray();
                for ($i = 0; $i < count($dates); $i++) {
                    $currentDate = $dates[$i];
                    $key = $currentDate->copy()->translatedFormat('F d, Y');
                    $index = $aggregation->search(function ($item) use ($currentDate) {
                        return Carbon::parse($item->max_date)->eq($currentDate->copy());
                    });
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->restoration_cost : null;
                }
                break;
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($preparedData);
    }

    public function showRatio(StatisticsShowRatio $request): JsonResponse
    {
        $aggregation = DamageNote::query()
            ->whereDate('damage_notes.date', '>=', $request->start_date)
            ->whereDate('damage_notes.date', '<=', $request->end_date)
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

        $preparedData = $aggregation->reduce(function ($carry, $item) {
            $carry[$item->name] = $item->restoration_cost;
            return $carry;
        }, []);

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($preparedData);
    }
}
