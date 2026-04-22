<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Statistics\ShowGlobal as StatisticsShowGlobal;
use App\Http\Requests\Statistics\ShowRatio as StatisticsShowRatio;
use App\Http\Requests\Statistics\ShowCube as StatisticsShowCube;
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
        self::MONTHLY_PERIOD => 'month',
    ];

    const OBJECTS_NUMBER_DIMENSION = 'objects_number';
    const RESTORATION_COST_DIMENSION = 'restoration_cost';

    const DIMENSION_TYPES = [
        self::OBJECTS_NUMBER_DIMENSION,
        self::RESTORATION_COST_DIMENSION,
    ];

    const DAY_CUBE_DIMENSION = 'day';
    const WEEK_CUBE_DIMENSION = 'week';
    const MONTH_CUBE_DIMENSION = 'month';
    const OBJECT_CATEGORY_CUBE_DIMENSION = 'object_category';
    const OBJECT_TYPE_CUBE_DIMENSION = 'object_type';
    const REGION_CUBE_DIMENSION = 'region';
    const DISTRICT_CUBE_DIMENSION = 'district';
    const COMMUNITY_CUBE_DIMENSION = 'community';
    const DAMAGE_TYPE_CUBE_DIMENSION = 'damage_type';

    const CUBE_DIMENSION_TYPES = [
        self::DAY_CUBE_DIMENSION,
        self::WEEK_CUBE_DIMENSION,
        self::MONTH_CUBE_DIMENSION,
        self::OBJECT_CATEGORY_CUBE_DIMENSION,
        self::OBJECT_TYPE_CUBE_DIMENSION,
        self::REGION_CUBE_DIMENSION,
        self::DISTRICT_CUBE_DIMENSION,
        self::COMMUNITY_CUBE_DIMENSION,
        self::DAMAGE_TYPE_CUBE_DIMENSION,
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

        $dataQuery = DamageNote::query()
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
                $dataQuery->groupBy(DB::raw("DATE_FORMAT(damage_notes.date, '%Y-%m')"));
                break;
            case self::WEEKLY_PERIOD:
                $dataQuery->groupBy(DB::raw("YEAR(damage_notes.date)"), DB::raw("WEEKOFYEAR(damage_notes.date)"));
                break;
            default:
                $dataQuery->groupBy(DB::raw("DATE(damage_notes.date)"));
                break;
        }

        $aggregation = null;
        if ($request->get('dimension_type') === self::RESTORATION_COST_DIMENSION) {
            $aggregation = $dataQuery
                ->select(DB::raw('MAX(damage_notes.date) AS max_date, SUM(damage_notes.restoration_cost) AS chart_value'))
                ->get();
        } else {
            $aggregation = $dataQuery
                ->select(DB::raw('MAX(damage_notes.date) AS max_date, COUNT(*) AS chart_value'))
                ->get();
        }

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
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->chart_value : null;
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
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->chart_value : null;
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
                    $preparedData[$key] = $index !== false ? $aggregation->get($index)->chart_value : null;
                }
                break;
        }

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($preparedData);
    }

    public function showRatio(StatisticsShowRatio $request): JsonResponse
    {
        $dataQuery = DamageNote::query()
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
            ->groupBy('damage_notes.object_type_id', 'object_types.name');

        $aggregation = null;
        if ($request->get('dimension_type') === self::RESTORATION_COST_DIMENSION) {
            $aggregation = $dataQuery
                ->select(DB::raw('object_types.name, SUM(damage_notes.restoration_cost) AS chart_value'))
                ->get();
        } else {
            $aggregation = $dataQuery
                ->select(DB::raw('object_types.name, COUNT(*) AS chart_value'))
                ->get();
        }

        $preparedData = $aggregation->reduce(function ($carry, $item) {
            $carry[$item->name] = $item->chart_value;
            return $carry;
        }, []);

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($preparedData);
    }

    public function showCube(StatisticsShowCube $request): JsonResponse
    {
        $dataQuery = DamageNote::query()
            ->when($request->get('start_date'), function($query) use (&$request) {
                $query->whereDate('damage_notes.date', '>=', $request->start_date);
            })
            ->when($request->get('end_date'), function($query) use (&$request) {
                $query->whereDate('damage_notes.date', '<=', $request->end_date);
            });

        switch($request->get('dimension_type')) {
            case self::DAY_CUBE_DIMENSION:
                $dataQuery->select(DB::raw("DATE_FORMAT(MAX(damage_notes.date), '%Y-%m-%d') AS title"))
                    ->groupBy(DB::raw("DATE(damage_notes.date)"));
                break;
            case self::WEEK_CUBE_DIMENSION:
                $dataQuery->select(DB::raw("CONCAT(
                    DATE_FORMAT(DATE_SUB(MAX(damage_notes.date), INTERVAL (DAYOFWEEK(MAX(damage_notes.date)) - 1) DAY), '%Y-%m-%d'),
                    ' - ',
                    DATE_FORMAT(DATE_ADD(MAX(damage_notes.date), INTERVAL (7 - DAYOFWEEK(MAX(damage_notes.date))) DAY), '%Y-%m-%d')
                ) AS title"))
                    ->groupBy(DB::raw("YEAR(damage_notes.date)"), DB::raw("WEEKOFYEAR(damage_notes.date)"));
                break;
            case self::MONTH_CUBE_DIMENSION:
                $dataQuery->select(DB::raw("DATE_FORMAT(MAX(damage_notes.date), '%Y-%m') AS title"))
                    ->groupBy(DB::raw("DATE_FORMAT(damage_notes.date, '%Y-%m')"));
                break;
            case self::OBJECT_CATEGORY_CUBE_DIMENSION:
                $dataQuery->select('object_categories.name AS title')
                    ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
                    ->join('object_categories', 'object_types.object_category_id', '=', 'object_categories.id')
                    ->groupBy('object_categories.id', 'object_categories.name');
                break;
            case self::OBJECT_TYPE_CUBE_DIMENSION:
                $dataQuery->select('object_types.name AS title')
                    ->join('object_types', 'damage_notes.object_type_id', '=', 'object_types.id')
                    ->groupBy('object_types.id', 'object_types.name');
                break;
            case self::REGION_CUBE_DIMENSION:
                $dataQuery->select('regions.name AS title')
                    ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->join('regions', 'districts.region_id', '=', 'regions.id')
                    ->groupBy('regions.id', 'regions.name');
                break;
            case self::DISTRICT_CUBE_DIMENSION:
                $dataQuery->select('districts.name AS title')
                    ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
                    ->join('districts', 'communities.district_id', '=', 'districts.id')
                    ->groupBy('districts.id', 'districts.name');
                break;
            case self::COMMUNITY_CUBE_DIMENSION:
                $dataQuery->select('communities.name AS title')
                    ->join('communities', 'damage_notes.community_id', '=', 'communities.id')
                    ->groupBy('communities.id', 'communities.name');
                break;
            case self::DAMAGE_TYPE_CUBE_DIMENSION:
                $dataQuery->select('damage_notes.damage_type AS title')
                    ->groupBy('damage_notes.damage_type');
            default:
                //
                break;
        }

        $preparedData = $dataQuery
            ->addSelect(DB::raw('SUM(damage_notes.restoration_cost) AS restoration_cost, COUNT(*) AS objects_number'))
            ->get();

        return $this->setDefaultSuccessResponse([])->respondWithSuccess($preparedData);
    }
}
