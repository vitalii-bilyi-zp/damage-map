<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RestorationCost\Predict as RestorationCostPredict;
use Illuminate\Support\Facades\Http;
use App\Models\ObjectType;
use App\Models\Community;
use App\Models\RepairType;
use App\Models\DamageNote;

use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;

class RestorationCostController extends Controller
{
    use ApiResponseHelpers;

    public function predict(RestorationCostPredict $request): JsonResponse
    {
        $data = $request->validated();

        $buildingType = optional(ObjectType::find($data['object_type_id']))->name ?? 'unknown';
        $community = Community::with('district.region')->find($data['community_id']);
        $region = $community?->district?->region?->name ?? 'unknown';
        $repairType = optional(RepairType::find($data['repair_type_id']))->name ?? 'unknown';
        $damageLevel = DamageNote::DAMAGE_TYPES_MAPPING[$data['damage_type']] ?? 'unknown';

        $payload = [
            'area' => (float) $data['area'],
            'floors' => (int) $data['floors'],
            'building_type' => (string) $buildingType,
            'damage_level' => (string) $damageLevel,
            'region' => (string) $region,
            'repair_type' => (string) $repairType,
        ];

        $url = rtrim(config('services.restoration.url'), '/') . '/predict';
        $apiKey = config('services.restoration.api_key');
        $timeout = (int) config('services.restoration.timeout', 8);

        try {
            $resp = Http::withHeaders([
                    'X-API-Key' => $apiKey,
                    'Accept' => 'application/json',
                ])
                ->timeout($timeout)
                ->post($url, $payload);

            if ($resp->failed()) {
                return $this->respondError('Flask request failed', [
                    'status' => $resp->status(),
                    'flask_error' => $resp->json(),
                ], 502);
            }

            $body = $resp->json();

            return $this->respondWithSuccess([
                'predicted_cost' => $body['predicted_cost'] ?? null,
                'currency' => $body['currency'] ?? 'UAH',
                'model' => $body['model'] ?? 'unknown',
            ]);
        } catch (\Throwable $e) {
            return $this->respondError('Upstream request failed', [
                'message' => $e->getMessage(),
            ], 502);
        }
    }
}
