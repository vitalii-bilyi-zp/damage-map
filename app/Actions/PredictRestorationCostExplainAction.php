<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use App\Models\ObjectType;
use App\Models\Community;
use App\Models\RepairType;
use App\Models\DamageNote;
use App\Exceptions\UpstreamRequestException;

class PredictRestorationCostExplainAction
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

        $url = rtrim(config('services.restoration.url'), '/') . '/predict_explain';
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
            $baseValue = isset($body['base_value']) ? (float) $body['base_value'] : null;
            $currency = $body['currency'] ?? 'UAH';
            $model = $body['model'] ?? 'unknown';
            $contributions = collect($body['contributions'] ?? [])
                ->map(function ($c) {
                    return [
                        'group' => (string) ($c['group'] ?? 'unknown'),
                        'percent' => isset($c['percent']) ? (float) $c['percent'] : 0.0,
                        'contribution' => array_key_exists('contribution', $c) && $c['contribution'] !== null
                            ? (float) $c['contribution']
                            : null,
                    ];
                })
                ->sortByDesc('percent')
                ->values()
                ->all();

            return [
                'predicted_cost' => $predicted,
                'currency' => $currency,
                'model' => $model,
                'base_value' => $baseValue,
                'contributions' => $contributions,
            ];
        } catch (UpstreamRequestException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
