<?php

namespace App\Actions;

use App\Models\Community;
use App\Models\ObjectType;
use App\Models\RepairType;
use App\Models\DamageNote;
use Illuminate\Support\Facades\Http;
use App\Exceptions\UpstreamRequestException;

class PredictRestorationCostAction
{
    public function execute(array $data): array
    {
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

        $payload['work_year'] = $data['work_year'] ?? null;
        $payload['work_month'] = $data['work_month'] ?? null;
        $payload['inflation_indices'] = \App\Models\InflationIndex::getAllForPayload();

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
                throw new UpstreamRequestException(
                    'Flask request failed',
                    $resp->status(),
                    $resp->json()
                );
            }

            $body = $resp->json() ?? [];

            $predicted = isset($body['predicted_cost']) ? (float) $body['predicted_cost'] : null;
            $currency = $body['currency'] ?? 'UAH';
            $model = $body['model'] ?? 'unknown';

            return [
                'predicted_cost' => $predicted,
                'adjusted_cost'  => $body['adjusted_cost']  ?? $predicted,
                'inflation_k'    => $body['inflation_k']    ?? 1.0,
                'base_year'      => $body['base_year']      ?? 2024,
                'work_year'      => $body['work_year']      ?? ($data['work_year']    ?? 2024),
                'work_month'     => $body['work_month']     ?? ($data['work_month'] ?? 1),
                'base_month'     => $body['base_month']     ?? 1,
                'currency'       => $currency,
                'model'          => $model,
            ];
        } catch (UpstreamRequestException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
