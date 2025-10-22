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
    /**
     * Sends a request to the restoration service and returns the prediction result.
     *
     * @param  array  $data  Validated data from the form/request.
     * @return array{predicted_cost: float|null, currency: string, model: string}
     *
     * @throws \App\Exceptions\UpstreamRequestException When the upstream service responds with an error (4xx/5xx).
     * @throws \Throwable When other network, parsing, or similar errors occur.
     */
    public function execute(array $data): array
    {
        $buildingType = optional(ObjectType::find($data['object_type_id']))->name ?? 'unknown';
        $community = Community::with('district.region')->find($data['community_id']);
        $region = $community?->district?->region?->name ?? 'unknown';
        $repairType = optional(RepairType::find($data['repair_type_id']))->name ?? 'unknown';
        $damageLevel = DamageNote::DAMAGE_TYPES_MAPPING[$data['damage_type']] ?? 'unknown';

        $payload = [
            'area'          => (float) $data['area'],
            'floors'        => (int) $data['floors'],
            'building_type' => (string) $buildingType,
            'damage_level'  => (string) $damageLevel,
            'region'        => (string) $region,
            'repair_type'   => (string) $repairType,
        ];

        $url = rtrim(config('services.restoration.url'), '/') . '/predict';
        $apiKey = config('services.restoration.api_key');
        $timeout = (int) config('services.restoration.timeout', 8);

        try {
            $resp = Http::withHeaders([
                    'X-API-Key' => $apiKey,
                    'Accept'    => 'application/json',
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

            return [
                'predicted_cost' => $body['predicted_cost'] ?? null,
                'currency'       => $body['currency'] ?? 'UAH',
                'model'          => $body['model'] ?? 'unknown',
            ];
        } catch (UpstreamRequestException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw $e;
        }
    }
}
