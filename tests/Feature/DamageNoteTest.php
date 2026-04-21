<?php

namespace Tests\Feature;

use App\Models\Community;
use App\Models\DamageNote;
use App\Models\DamageNoteRequest;
use App\Models\District;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

class DamageNoteTest extends TestCase
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

    /**
     * Create a fully-approved DamageNote (with its DamageNoteRequest).
     */
    private function createApprovedNote(array $noteAttributes = []): DamageNote
    {
        $note    = DamageNote::factory()->create($noteAttributes);
        DamageNoteRequest::factory()->approved()->create(['damage_note_id' => $note->id]);

        return $note;
    }

    /**
     * Create an admin user (no territorial restriction by default).
     */
    private function createAdmin(array $attributes = []): User
    {
        $user = User::factory()->create(array_merge(['api_token' => Str::random(60)], $attributes));
        $user->assignRole('admin');

        return $user;
    }

    // -------------------------------------------------------------------------
    // GET /api/damage-notes/approved
    // -------------------------------------------------------------------------

    public function test_unauthenticated_user_cannot_access_approved_damage_notes_returns_401(): void
    {
        $response = $this->getJson('/api/damage-notes/approved');

        $response->assertStatus(401);
    }

    public function test_authenticated_admin_gets_approved_damage_notes_with_correct_json_structure(): void
    {
        $admin = $this->createAdmin();
        $this->createApprovedNote();

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/damage-notes/approved');

        // f9web respondWithSuccess returns the collection at the root (no 'data' wrapper)
        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'id',
                    'date',
                    'object_type_id',
                    'community_id',
                    'community',
                    'object_type',
                    'city',
                    'street',
                    'floors',
                    'area',
                    'damage_type',
                    'restoration_cost',
                ],
            ]);
    }

    // -------------------------------------------------------------------------
    // PUT /api/damage-notes/{id}
    // -------------------------------------------------------------------------

    public function test_admin_can_update_damage_note(): void
    {
        $this->fakeFlask();

        $admin = $this->createAdmin();
        $note  = $this->createApprovedNote(['city' => 'OldCity']);

        $response = $this->actingAs($admin, 'api')
            ->putJson("/api/damage-notes/{$note->id}", [
                'city' => 'NewCity',
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('damage_notes', [
            'id'   => $note->id,
            'city' => 'NewCity',
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE /api/damage-notes/{id}
    // -------------------------------------------------------------------------

    public function test_admin_can_delete_damage_note(): void
    {
        $admin = $this->createAdmin();
        $note  = $this->createApprovedNote();

        $response = $this->actingAs($admin, 'api')
            ->deleteJson("/api/damage-notes/{$note->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('damage_notes', ['id' => $note->id]);
    }

    // -------------------------------------------------------------------------
    // Region-scoped visibility
    // -------------------------------------------------------------------------

    public function test_admin_with_region_id_sees_only_notes_from_their_region(): void
    {
        // Build two separate geographic hierarchies
        $regionA   = Region::factory()->create();
        $districtA = District::factory()->create(['region_id' => $regionA->id]);
        $communityA = Community::factory()->create(['district_id' => $districtA->id]);

        $regionB    = Region::factory()->create();
        $districtB  = District::factory()->create(['region_id' => $regionB->id]);
        $communityB = Community::factory()->create(['district_id' => $districtB->id]);

        // Admin belongs to region A
        $admin = $this->createAdmin(['region_id' => $regionA->id]);

        // One approved note in region A, one in region B
        $noteA = $this->createApprovedNote(['community_id' => $communityA->id]);
        $noteB = $this->createApprovedNote(['community_id' => $communityB->id]);

        $response = $this->actingAs($admin, 'api')
            ->getJson('/api/damage-notes/approved');

        $response->assertStatus(200);

        // Root-level array (no 'data' wrapper)
        $ids = collect($response->json())->pluck('id')->toArray();

        $this->assertContains($noteA->id, $ids, 'Note from own region should be visible');
        $this->assertNotContains($noteB->id, $ids, 'Note from other region should NOT be visible');
    }
}
