<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestorationCost\Predict as RestorationCostPredict;
use App\Actions\PredictRestorationCostExplainAction;
use App\Exceptions\UpstreamRequestException;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

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
