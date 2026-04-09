<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestorationCost\Predict as RestorationCostPredict;
use App\Actions\PredictRestorationCostExplainAction;
use App\Exceptions\UpstreamRequestException;

use App\Actions\PredictRestorationCostAction;
use App\Models\InflationIndex;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestorationCostController extends Controller
{
    use ApiResponseHelpers;

    public function predict(RestorationCostPredict $request, PredictRestorationCostExplainAction $action): JsonResponse
    {
        $data = $request->validated();

        try {
            $result = $action->execute($data);

            $pieObject = $this->makePieObjectFromContributions($result['contributions'] ?? null);

            return $this->respondWithSuccess(array_merge($result, [
                'pie' => $pieObject, // null если contributions отсутствуют
            ]));

        } catch (UpstreamRequestException $e) {
            return $this->respondError('Flask request failed', [
                'status' => $e->status(),
                'flask_error' => $e->response(),
            ], 502);
        } catch (\Throwable $e) {
            return $this->respondError('Upstream request failed', [
                'message' => $e->getMessage(),
            ], 502);
        }
    }

    public function scenarioComparison(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'object_type_id'     => 'required|integer|exists:object_types,id',
            'community_id'       => 'required|integer|exists:communities,id',
            'repair_type_id'     => 'required|integer|exists:repair_types,id',
            'damage_type'        => 'required|string',
            'area'               => 'required|numeric|min:1|max:1000000',
            'floors'             => 'required|integer|min:1|max:100',
            'periods'            => 'required|array|min:1|max:72',
            'periods.*.year'     => 'required|integer|min:2020|max:2040',
            'periods.*.month'    => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $scenarios = [];

        foreach ($validated['periods'] as $period) {
            $data = array_merge($validated, [
                'work_year'    => $period['year'],
                'work_month'   => $period['month'],
            ]);
            unset($data['periods']);

            try {
                $result = app(PredictRestorationCostAction::class)->execute($data);
            } catch (UpstreamRequestException $e) {
                return $this->respondError('Flask request failed', [
                    'status'      => $e->status(),
                    'flask_error' => $e->response(),
                ], 502);
            } catch (\Throwable $e) {
                return $this->respondError('Upstream request failed', [
                    'message' => $e->getMessage(),
                ], 502);
            }

            $result['year']    = $period['year'];
            $result['month']   = $period['month'];

            if (count($scenarios) > 0) {
                $base = $scenarios[0]['adjusted_cost'];
                $result['delta_pct'] = $base > 0
                    ? round(($result['adjusted_cost'] - $base) / $base * 100, 1)
                    : 0;
            } else {
                $result['delta_pct'] = 0;
            }

            $scenarios[] = $result;
        }

        return $this->respondWithSuccess(['scenarios' => $scenarios]);
    }

    public function inflationIndices(): JsonResponse
    {
        return $this->respondWithSuccess(InflationIndex::orderBy('year')->orderBy('month')->get());
    }

    private function makePieObjectFromContributions(?array $contributions): ?array
    {
        if (empty($contributions) || !is_array($contributions)) {
            return null;
        }

        $uaMap = [
            'region' => 'Регіон',
            'building_type' => 'Тип об\'єкта',
            'floors' => 'Кількість поверхів',
            'area' => 'Площа',
            'damage_level' => 'Тип пошкодження',
            'repair_type' => 'Тип ремонту',
        ];

        $items = array_values(array_filter($contributions, fn ($c) =>
            isset($c['group']) && isset($c['percent'])
        ));
        usort($items, fn ($a, $b) => ($b['percent'] ?? 0) <=> ($a['percent'] ?? 0));

        $pie = [];
        foreach ($items as $c) {
            $group = (string) $c['group'];
            $group = str_replace("\ufeff", '', $group);
            $group = trim($group);
            $uaKey = $uaMap[$group] ?? $group;
            $pie[$uaKey] = (float) $c['percent'];
        }

        return $pie;
    }
}
