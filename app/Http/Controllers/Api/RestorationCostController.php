<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestorationCost\Predict as RestorationCostPredict;
use App\Actions\PredictRestorationCostAction;
use App\Exceptions\UpstreamRequestException;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RestorationCostController extends Controller
{
    use ApiResponseHelpers;

    public function predict(RestorationCostPredict $request, PredictRestorationCostAction $action): JsonResponse
    {
        $data = $request->validated();

        try {
            $result = $action->execute($data);

            return $this->respondWithSuccess($result);
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
}
