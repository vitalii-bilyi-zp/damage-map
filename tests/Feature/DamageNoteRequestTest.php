<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use App\Models\ObjectType;
use App\Models\RepairType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DamageNoteRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->createRole('super_admin');
        $this->createRole('admin');
        $this->createRole('analyst');
    }

    /**
     * Fake the Flask prediction service so tests never make real HTTP calls.
     */
    private function fakeFlask(): void
    {
        Http::fake([
            '*' => Http::response(['predicted_cost' => 500000.0], 200),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/damage-note-requests
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_can_create_damage_note_request_with_valid_data(): void
    {
        $this->fakeFlask();

        $objectType = ObjectType::factory()->create();
        $community  = Community::factory()->create();
        $repairType = RepairType::factory()->create();

        $payload = [
            'date'           => '2023-05-15',
            'object_type_id' => $objectType->id,
            'community_id'   => $community->id,
            'repair_type_id' => $repairType->id,
            'floors'         => 3,
            'area'           => 250.00,
            'damage_type'    => 'medium',
            'city'           => 'Kyiv',
            'street'         => 'Khreshchatyk',
            'building_number'=> '1',
            'full_name'      => 'Ivan Petrenko',
            'email'          => 'ivan@example.com',
            'phone'          => '+380501234567',
        ];

        $response = $this->postJson('/api/damage-note-requests', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('damage_notes', [
            'city'   => 'Kyiv',
            'street' => 'Khreshchatyk',
        ]);

        $this->assertDatabaseHas('damage_note_requests', [
            'full_name' => 'Ivan Petrenko',
            'email'     => 'ivan@example.com',
        ]);
    }

    public function test_creating_damage_note_request_with_invalid_data_returns_422(): void
    {
        // Missing required fields: date, object_type_id, community_id, floors, area
        // Also an invalid date format to trigger date validation
        $payload = [
            'full_name' => 'Ivan Petrenko',
            'date'      => 'not-a-date',
        ];

        $response = $this->postJson('/api/damage-note-requests', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['date', 'object_type_id', 'community_id', 'floors', 'area']);
    }

    // -------------------------------------------------------------------------
    // POST /api/damage-note-requests/{id}/approve
    // -------------------------------------------------------------------------

    public function test_super_admin_can_approve_damage_note_request(): void
    {
        // DamageNoteRequestPolicy::approveRequest() returns false for everyone.
        // Gate::before() in AuthServiceProvider grants super_admin unconditional access.
        $superAdmin = User::factory()->create(['api_token' => Str::random(60)]);
        $superAdmin->assignRole('super_admin');

        $request = DamageNoteRequest::factory()->pending()->create();

        $response = $this->actingAs($superAdmin, 'api')
            ->postJson("/api/damage-note-requests/{$request->id}/approve");

        $response->assertStatus(200);

        $this->assertNotNull($request->fresh()->approved_at);
        $this->assertNull($request->fresh()->declined_at);
        $this->assertEquals($superAdmin->id, $request->fresh()->approver_id);
    }

    // -------------------------------------------------------------------------
    // POST /api/damage-note-requests/{id}/decline
    // -------------------------------------------------------------------------

    public function test_super_admin_can_decline_damage_note_request_with_comment(): void
    {
        $superAdmin = User::factory()->create(['api_token' => Str::random(60)]);
        $superAdmin->assignRole('super_admin');

        $request = DamageNoteRequest::factory()->pending()->create();

        $response = $this->actingAs($superAdmin, 'api')
            ->postJson("/api/damage-note-requests/{$request->id}/decline", [
                'comment' => 'Insufficient evidence provided.',
            ]);

        $response->assertStatus(200);

        $this->assertNotNull($request->fresh()->declined_at);
        $this->assertNull($request->fresh()->approved_at);
        $this->assertEquals('Insufficient evidence provided.', $request->fresh()->approver_comment);
        $this->assertEquals($superAdmin->id, $request->fresh()->approver_id);
    }

    // -------------------------------------------------------------------------
    // Authorization — analyst role
    // -------------------------------------------------------------------------

    public function test_analyst_cannot_approve_damage_note_request_returns_403(): void
    {
        $analyst = User::factory()->create(['api_token' => Str::random(60)]);
        $analyst->assignRole('analyst');

        $request = DamageNoteRequest::factory()->pending()->create();

        $response = $this->actingAs($analyst, 'api')
            ->postJson("/api/damage-note-requests/{$request->id}/approve");

        $response->assertStatus(403);
    }

    public function test_analyst_cannot_decline_damage_note_request_returns_403(): void
    {
        $analyst = User::factory()->create(['api_token' => Str::random(60)]);
        $analyst->assignRole('analyst');

        $request = DamageNoteRequest::factory()->pending()->create();

        $response = $this->actingAs($analyst, 'api')
            ->postJson("/api/damage-note-requests/{$request->id}/decline", [
                'comment' => 'Should not work.',
            ]);

        $response->assertStatus(403);
    }
}
