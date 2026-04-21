<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Spatie Permission requires roles to exist with the correct guard
        $this->createRole('admin', 'Admin');
    }

    // -------------------------------------------------------------------------
    // POST /api/login
    // -------------------------------------------------------------------------

    public function test_login_with_valid_credentials_returns_200_with_access_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'admin@example.com',
            'password' => 'secret123',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.email', 'admin@example.com')
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'access_token'],
            ]);

        $this->assertNotNull($response->json('user.access_token'));
    }

    public function test_login_with_invalid_credentials_returns_401(): void
    {
        User::factory()->create([
            'email'    => 'admin@example.com',
            'password' => bcrypt('correct_password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email'    => 'admin@example.com',
            'password' => 'wrong_password',
        ]);

        $response->assertStatus(401);
    }

    // -------------------------------------------------------------------------
    // GET /api/user
    // -------------------------------------------------------------------------

    public function test_authenticated_user_can_get_their_own_data(): void
    {
        $user = User::factory()->create([
            'api_token' => Str::random(60),
        ]);
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/user');

        $response->assertStatus(200)
            ->assertJsonPath('user.id', $user->id)
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonStructure([
                'user' => ['id', 'name', 'email', 'access_token', 'roles'],
            ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/logout
    // -------------------------------------------------------------------------

    public function test_logout_clears_api_token_and_returns_200(): void
    {
        $user = User::factory()->create([
            'api_token' => Str::random(60),
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertNull($user->fresh()->api_token);
    }
}
