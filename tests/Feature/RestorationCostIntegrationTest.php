<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use App\Models\InflationIndex;
use App\Models\ObjectType;
use App\Models\RepairType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Інтеграційні тести взаємодії Laravel-додатку з Python ML-мікросервісом.
 *
 * ВАЖЛИВО: у реальному коді ML-сервіс викликається при СТВОРЕННІ заявки
 * (POST /api/damage-note-requests), а не при схваленні. approveRequest()
 * лише встановлює approver_id та approved_at і не звертається до Flask.
 *
 * Тести покривають:
 *   – store: успішне збереження predicted_restoration_cost з відповіді ML
 *   – store: поведінка при недоступності ML (timeout / 500 / 400)
 *   – POST /api/predict-restoration-cost (explain endpoint)
 *   – POST /api/predict-restoration-cost/scenario-comparison
 *   – approve: успішне схвалення (без ML)
 */
class RestorationCostIntegrationTest extends TestCase
{
    use RefreshDatabase;

    // ── Стандартна відповідь Flask для /predict ──────────────────────────────

    private const FLASK_PREDICT_RESPONSE = [
        'predicted_cost' => 1_250_000.0,
        'adjusted_cost'  => 1_312_500.0,
        'inflation_k'    => 1.05,
        'base_year'      => 2024,
        'work_year'      => 2025,
        'work_month'     => 3,
        'base_month'     => 1,
        'currency'       => 'UAH',
        'model'          => 'gradient_boosting_v2',
    ];

    // Відповідь Flask для /predict_explain (з contributions)
    private const FLASK_EXPLAIN_RESPONSE = [
        'predicted_cost' => 1_250_000.0,
        'adjusted_cost'  => 1_312_500.0,
        'inflation_k'    => 1.05,
        'base_year'      => 2024,
        'work_year'      => 2025,
        'work_month'     => 3,
        'base_month'     => 1,
        'currency'       => 'UAH',
        'model'          => 'gradient_boosting_v2',
        'base_value'     => 1_190_000.0,
        'contributions'  => [
            ['group' => 'area',          'percent' => 35.2, 'contribution' => 440_000.0],
            ['group' => 'building_type', 'percent' => 28.1, 'contribution' => 351_250.0],
            ['group' => 'damage_level',  'percent' => 20.4, 'contribution' => 255_000.0],
            ['group' => 'region',        'percent' => 10.0, 'contribution' => 125_000.0],
            ['group' => 'repair_type',   'percent' =>  4.3, 'contribution' =>  53_750.0],
            ['group' => 'floors',        'percent' =>  2.0, 'contribution' =>  25_000.0],
        ],
    ];

    // ─────────────────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        parent::setUp();

        $this->createRole('super_admin');
        $this->createRole('admin');
        $this->createRole('analyst');

        // Очищаємо кеш inflation_indices між тестами
        Cache::forget('inflation_indices_payload');
    }

    // ── Допоміжні методи ─────────────────────────────────────────────────────

    /** Повертає базовий валідний payload для POST /api/damage-note-requests */
    private function validStorePayload(ObjectType $objectType, Community $community, RepairType $repairType): array
    {
        return [
            'date'            => '2023-06-01',
            'object_type_id'  => $objectType->id,
            'community_id'    => $community->id,
            'repair_type_id'  => $repairType->id,
            'floors'          => 4,
            'area'            => 320.50,
            'damage_type'     => 'medium',
            'city'            => 'Харків',
            'street'          => 'Сумська',
            'building_number' => '12',
            'full_name'       => 'Олена Бондаренко',
            'email'           => 'elena@example.com',
            'phone'           => '+380671234567',
        ];
    }

    /** Повертає базовий валідний payload для POST /api/predict-restoration-cost */
    private function validPredictPayload(ObjectType $objectType, Community $community, RepairType $repairType): array
    {
        return [
            'object_type_id' => $objectType->id,
            'community_id'   => $community->id,
            'repair_type_id' => $repairType->id,
            'floors'         => 5,
            'area'           => 500.0,
            'damage_type'    => 'high',
        ];
    }

    /** Створює авторизованого super_admin користувача */
    private function makeSuperAdmin(): User
    {
        $user = User::factory()->create(['api_token' => Str::random(60)]);
        $user->assignRole('super_admin');
        return $user;
    }

    // =========================================================================
    // 1. test_store_request_calls_ml_service_and_saves_predicted_cost
    //    ML повертає 200 → predicted_restoration_cost збережено в БД,
    //    Laravel надіслав POST на /predict з коректним payload
    // =========================================================================

    public function test_store_request_calls_ml_service_and_saves_predicted_cost(): void
    {
        Http::fake([
            '*/predict' => Http::response(self::FLASK_PREDICT_RESPONSE, 200),
        ]);

        $objectType = ObjectType::factory()->create(['name' => 'Житловий будинок']);
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create(['name' => 'Капітальний']);

        $response = $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        );

        $response->assertStatus(200);

        // Перевіряємо, що Laravel надіслав POST-запит до Flask /predict
        Http::assertSent(function ($request) use ($objectType, $repairType) {
            $body = $request->data();

            return str_ends_with($request->url(), '/predict')
                && $request->method() === 'POST'
                && $body['area']           === 320.50
                && $body['floors']         === 4
                && $body['building_type']  === $objectType->name
                && $body['damage_level']   === DamageNote::DAMAGE_TYPES_MAPPING['medium']
                && $body['repair_type']    === $repairType->name
                && array_key_exists('inflation_indices', $body)
                && is_array($body['inflation_indices']);
        });

        // Перевіряємо що predicted_restoration_cost збережено в таблиці damage_notes
        $this->assertDatabaseHas('damage_notes', [
            'city'                        => 'Харків',
            'street'                      => 'Сумська',
            'predicted_restoration_cost'  => 1_250_000.0,
        ]);

        // Перевіряємо що DamageNoteRequest створено
        $this->assertDatabaseHas('damage_note_requests', [
            'email'     => 'elena@example.com',
            'full_name' => 'Олена Бондаренко',
        ]);
    }

    // =========================================================================
    // 2. test_store_request_sends_correct_region_in_ml_payload
    //    Регіон повинен братися з ланцюжка community → district → region
    // =========================================================================

    public function test_store_request_sends_correct_region_in_ml_payload(): void
    {
        Http::fake([
            '*/predict' => Http::response(self::FLASK_PREDICT_RESPONSE, 200),
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        // Отримуємо назву регіону через відношення community → district → region
        $community->load('district.region');
        $expectedRegion = $community->district->region->name;

        $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        )->assertStatus(200);

        Http::assertSent(function ($request) use ($expectedRegion) {
            return str_ends_with($request->url(), '/predict')
                && $request->data()['region'] === $expectedRegion;
        });
    }

    // =========================================================================
    // 3. test_store_request_sends_api_key_header_to_ml_service
    //    Laravel повинен надсилати X-API-Key заголовок до Flask
    // =========================================================================

    public function test_store_request_sends_api_key_header_to_ml_service(): void
    {
        config(['services.restoration.api_key' => 'test-secret-key-12345']);

        Http::fake([
            '*/predict' => Http::response(self::FLASK_PREDICT_RESPONSE, 200),
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        )->assertStatus(200);

        Http::assertSent(function ($request) {
            return $request->hasHeader('X-API-Key', 'test-secret-key-12345')
                && $request->hasHeader('Accept', 'application/json');
        });
    }

    // =========================================================================
    // 4. test_store_request_handles_ml_service_500_error
    //    ML повертає 500 → контролер викликає respondError() → HTTP 400
    //    (F9Web ApiResponseHelpers::respondError завжди повертає HTTP 400,
    //     додаткові параметри статусу ігноруються)
    // =========================================================================

    public function test_store_request_handles_ml_service_500_error(): void
    {
        Http::fake([
            '*/predict' => Http::response(['error' => 'Internal server error'], 500),
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $response = $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        );

        $response->assertStatus(400)
            ->assertJson(['error' => 'Flask request failed']);

        // Жодного damage_note не створено (транзакція не розпочалась)
        $this->assertDatabaseCount('damage_notes', 0);
        $this->assertDatabaseCount('damage_note_requests', 0);
    }

    // =========================================================================
    // 5. test_store_request_handles_ml_service_400_validation_error
    //    ML повертає 400 → UpstreamRequestException → respondError → HTTP 400
    // =========================================================================

    public function test_store_request_handles_ml_service_400_validation_error(): void
    {
        Http::fake([
            '*/predict' => Http::response(['error' => 'Invalid input data', 'detail' => 'area must be positive'], 400),
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $response = $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        );

        $response->assertStatus(400)
            ->assertJson(['error' => 'Flask request failed']);

        $this->assertDatabaseCount('damage_notes', 0);
    }

    // =========================================================================
    // 6. test_store_request_handles_ml_service_connection_timeout
    //    ML недоступний (ConnectionException) → respondError → HTTP 400
    // =========================================================================

    public function test_store_request_handles_ml_service_connection_timeout(): void
    {
        Http::fake([
            '*/predict' => function () {
                throw new ConnectionException('Connection timed out after 8 seconds');
            },
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $response = $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        );

        $response->assertStatus(400)
            ->assertJson(['error' => 'Upstream request failed']);

        $this->assertDatabaseCount('damage_notes', 0);
    }

    // =========================================================================
    // 7. test_store_request_includes_inflation_indices_in_ml_payload
    //    Коли InflationIndex записи існують, вони надсилаються у payload до ML
    // =========================================================================

    public function test_store_request_includes_inflation_indices_in_ml_payload(): void
    {
        // Створюємо кілька записів індексів інфляції
        InflationIndex::create(['year' => 2024, 'month' => 1, 'index_value' => 1.082, 'source_type' => 'official']);
        InflationIndex::create(['year' => 2024, 'month' => 2, 'index_value' => 1.074, 'source_type' => 'official']);
        InflationIndex::create(['year' => 2025, 'month' => 1, 'index_value' => 1.091, 'source_type' => 'forecast']);

        Cache::forget('inflation_indices_payload');

        Http::fake([
            '*/predict' => Http::response(self::FLASK_PREDICT_RESPONSE, 200),
        ]);

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $this->postJson(
            '/api/damage-note-requests',
            $this->validStorePayload($objectType, $community, $repairType)
        )->assertStatus(200);

        Http::assertSent(function ($request) {
            $indices = $request->data()['inflation_indices'] ?? [];
            return count($indices) === 3
                && $indices[0]['year'] === 2024
                && $indices[0]['month'] === 1
                && (float) $indices[0]['index_value'] === 1.082;
        });
    }

    // =========================================================================
    // 8. test_predict_restoration_cost_endpoint
    //    POST /api/predict-restoration-cost (auth required)
    //    ML повертає 200 з contributions → відповідь містить predicted_cost і pie
    // =========================================================================

    public function test_predict_restoration_cost_endpoint(): void
    {
        Http::fake([
            '*/predict_explain' => Http::response(self::FLASK_EXPLAIN_RESPONSE, 200),
        ]);

        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost', $this->validPredictPayload($objectType, $community, $repairType));

        // respondWithSuccess($data) повертає $data напряму (без обгортки {"data": ...})
        // JSON-числа без дробової частини десеріалізуються як int, тому порівнюємо без .0
        $response->assertStatus(200)
            ->assertJsonPath('predicted_cost', 1_250_000)
            ->assertJsonPath('adjusted_cost', 1_312_500)
            ->assertJsonPath('currency', 'UAH')
            ->assertJsonPath('model', 'gradient_boosting_v2')
            ->assertJsonStructure([
                'predicted_cost',
                'adjusted_cost',
                'inflation_k',
                'base_year',
                'work_year',
                'work_month',
                'currency',
                'model',
                'contributions',
                'pie',
            ]);

        // pie повинен бути об'єктом із украномовними ключами
        $pie = $response->json('pie');
        $this->assertIsArray($pie);
        $this->assertNotEmpty($pie);
    }

    // =========================================================================
    // 9. test_predict_endpoint_with_work_year_and_month
    //    Необов'язкові work_year / work_month передаються до Flask
    // =========================================================================

    public function test_predict_endpoint_with_work_year_and_month(): void
    {
        Http::fake([
            '*/predict_explain' => Http::response(self::FLASK_EXPLAIN_RESPONSE, 200),
        ]);

        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $payload = array_merge(
            $this->validPredictPayload($objectType, $community, $repairType),
            ['work_year' => 2026, 'work_month' => 6]
        );

        $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost', $payload)
            ->assertStatus(200);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return str_ends_with($request->url(), '/predict_explain')
                && $body['work_year'] === 2026
                && $body['work_month'] === 6;
        });
    }

    // =========================================================================
    // 10. test_predict_endpoint_handles_ml_service_unavailable
    //     ML повертає 500 → UpstreamRequestException → respondError → HTTP 400
    // =========================================================================

    public function test_predict_endpoint_handles_ml_service_unavailable(): void
    {
        Http::fake([
            '*/predict_explain' => Http::response(['error' => 'Model not loaded'], 500),
        ]);

        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost', $this->validPredictPayload($objectType, $community, $repairType))
            ->assertStatus(400)
            ->assertJson(['error' => 'Flask request failed']);
    }

    // =========================================================================
    // 11. test_predict_endpoint_requires_authentication
    //     Без авторизації → 401
    // =========================================================================

    public function test_predict_endpoint_requires_authentication(): void
    {
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $this->postJson(
            '/api/predict-restoration-cost',
            $this->validPredictPayload($objectType, $community, $repairType)
        )->assertStatus(401);
    }

    // =========================================================================
    // 12. test_predict_endpoint_validates_request_fields
    //     Відсутні обов'язкові поля → 422
    // =========================================================================

    public function test_predict_endpoint_validates_request_fields(): void
    {
        $user = $this->makeSuperAdmin();

        $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['object_type_id', 'community_id', 'repair_type_id', 'floors', 'area', 'damage_type']);
    }

    // =========================================================================
    // 13. test_scenario_comparison_endpoint
    //     POST /api/predict-restoration-cost/scenario-comparison
    //     ML викликається по одному разу на кожен period
    //     Відповідь містить масив scenarios з delta_pct
    // =========================================================================

    public function test_scenario_comparison_endpoint(): void
    {
        // Кожен виклик /predict повертає однакову відповідь
        Http::fake([
            '*/predict' => Http::response(self::FLASK_PREDICT_RESPONSE, 200),
        ]);

        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $payload = [
            'object_type_id' => $objectType->id,
            'community_id'   => $community->id,
            'repair_type_id' => $repairType->id,
            'damage_type'    => 'high',
            'area'           => 400.0,
            'floors'         => 3,
            'periods'        => [
                ['year' => 2025, 'month' => 1],
                ['year' => 2025, 'month' => 6],
                ['year' => 2026, 'month' => 1],
            ],
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost/scenario-comparison', $payload);

        // respondWithSuccess(['scenarios' => ...]) повертає {"scenarios": [...]} напряму
        $response->assertStatus(200)
            ->assertJsonStructure([
                'scenarios' => [
                    '*' => [
                        'predicted_cost',
                        'adjusted_cost',
                        'inflation_k',
                        'year',
                        'month',
                        'delta_pct',
                    ],
                ],
            ]);

        $scenarios = $response->json('scenarios');
        $this->assertCount(3, $scenarios);

        // Перший сценарій — базовий, delta_pct = 0
        $this->assertEquals(0, $scenarios[0]['delta_pct']);

        // Перевіряємо що ML було викликано 3 рази (по одному на кожен period)
        Http::assertSentCount(3);
    }

    // =========================================================================
    // 14. test_scenario_comparison_handles_ml_service_failure
    //     Якщо ML повертає 500 для будь-якого period → 502
    // =========================================================================

    public function test_scenario_comparison_handles_ml_service_failure(): void
    {
        Http::fake([
            '*/predict' => Http::response(['error' => 'Service unavailable'], 503),
        ]);

        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $payload = [
            'object_type_id' => $objectType->id,
            'community_id'   => $community->id,
            'repair_type_id' => $repairType->id,
            'damage_type'    => 'low',
            'area'           => 200.0,
            'floors'         => 2,
            'periods'        => [
                ['year' => 2025, 'month' => 3],
            ],
        ];

        $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost/scenario-comparison', $payload)
            ->assertStatus(400)
            ->assertJson(['error' => 'Flask request failed']);
    }

    // =========================================================================
    // 15. test_scenario_comparison_validates_periods
    //     periods — обов'язкове поле, мінімум 1 елемент
    // =========================================================================

    public function test_scenario_comparison_validates_periods(): void
    {
        $user       = $this->makeSuperAdmin();
        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $this->actingAs($user, 'api')
            ->postJson('/api/predict-restoration-cost/scenario-comparison', [
                'object_type_id' => $objectType->id,
                'community_id'   => $community->id,
                'repair_type_id' => $repairType->id,
                'damage_type'    => 'low',
                'area'           => 200.0,
                'floors'         => 2,
                // periods відсутній
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['periods']);
    }

    // =========================================================================
    // 16. test_approve_request_sets_approved_at_and_approver_id
    //     approveRequest() НЕ викликає ML-сервіс,
    //     лише оновлює approved_at та approver_id
    // =========================================================================

    public function test_approve_request_sets_approved_at_and_approver_id(): void
    {
        // Http::fake() НЕ налаштовано — якщо Laravel зробить реальний HTTP-виклик,
        // тест впаде. Це гарантує що approve не взаємодіє з ML.
        Http::fake(); // перехоплює все, але не дозволяє реальних запитів

        $superAdmin = $this->makeSuperAdmin();
        $request    = DamageNoteRequest::factory()->pending()->create();

        $this->actingAs($superAdmin, 'api')
            ->postJson("/api/damage-note-requests/{$request->id}/approve")
            ->assertStatus(200);

        $fresh = $request->fresh();
        $this->assertNotNull($fresh->approved_at);
        $this->assertNull($fresh->declined_at);
        $this->assertEquals($superAdmin->id, $fresh->approver_id);

        // ML не викликався під час approve
        Http::assertNothingSent();
    }

    // =========================================================================
    // 17. test_approve_does_not_overwrite_predicted_restoration_cost
    //     predicted_restoration_cost, збережена під час store, не змінюється після approve
    // =========================================================================

    public function test_approve_does_not_overwrite_predicted_restoration_cost(): void
    {
        Http::fake();

        $superAdmin  = $this->makeSuperAdmin();
        $damageNote  = DamageNote::factory()->create(['predicted_restoration_cost' => 987_654.32]);
        $noteRequest = DamageNoteRequest::factory()->pending()->create(['damage_note_id' => $damageNote->id]);

        $this->actingAs($superAdmin, 'api')
            ->postJson("/api/damage-note-requests/{$noteRequest->id}/approve")
            ->assertStatus(200);

        // Значення в БД не змінилося
        $this->assertDatabaseHas('damage_notes', [
            'id'                         => $damageNote->id,
            'predicted_restoration_cost' => 987_654.32,
        ]);
    }
}
