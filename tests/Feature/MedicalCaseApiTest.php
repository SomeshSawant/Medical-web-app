<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MedicalCaseApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * API Test 1: POST /api/login issues a valid token.
     */
    public function test_api_login_returns_token_and_user_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'api-admin@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api-admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Authentication successful.',
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'token',
                    'token_type',
                    'user' => ['id', 'name', 'email', 'role'],
                ],
            ]);
    }

    /**
     * API Test 2: POST /api/login rejects invalid credentials with 401.
     */
    public function test_api_login_rejects_invalid_credentials_with_401(): void
    {
        User::factory()->create([
            'email' => 'api-admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'api-admin@example.com',
            'password' => 'incorrect-password',
        ]);

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Invalid email or password credentials.',
            ]);
    }

    /**
     * API Test 3: POST /api/logout revokes the token.
     */
    public function test_api_logout_revokes_token(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'API token revoked successfully.',
            ]);

        $this->assertCount(0, $user->tokens);
    }

    /**
     * API Test 4: GET /api/cases lists cases paginated with consistent shape.
     */
    public function test_api_cases_list_returns_paginated_data(): void
    {
        $viewer = User::factory()->viewer()->create();
        MedicalCase::factory()->count(15)->create(['created_by' => $viewer->id]);

        $token = $viewer->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/cases');

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ])
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'current_page',
                    'data',
                    'total',
                    'per_page',
                ],
            ]);

        $this->assertEquals(10, count($response->json('data.data')));
    }

    /**
     * API Test 5: POST /api/cases creates case for Admin (201).
     */
    public function test_api_admin_can_create_case(): void
    {
        $admin = User::factory()->admin()->create();
        $token = $admin->createToken('admin-token')->plainTextToken;

        $payload = [
            'case_number' => 'CASE-API-200',
            'patient_reference' => 'PAT-API-01',
            'surgeon_name' => 'Dr. Lisa Cuddy',
            'implant_type' => 'Acetabular Cup System',
            'status' => MedicalCase::STATUS_PLANNING,
            'priority' => MedicalCase::PRIORITY_HIGH,
            'surgery_date' => '2026-11-15',
        ];

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/cases', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'case_number' => 'CASE-API-200',
                    'patient_reference' => 'PAT-API-01',
                ],
            ]);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-API-200',
            'created_by' => $admin->id,
        ]);
    }

    /**
     * API Test 6: GET /api/cases/{id} returns single case (200) or 404.
     */
    public function test_api_get_single_case_and_not_found(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-API-GET',
            'created_by' => $admin->id,
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        // Existing case
        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson("/api/cases/{$case->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'id' => $case->id,
                    'case_number' => 'CASE-API-GET',
                ],
            ]);

        // Non-existent case (404)
        $notFoundResponse = $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/cases/99999');

        $notFoundResponse->assertStatus(404)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * API Test 7: PUT /api/cases/{id} updates case for Admin.
     */
    public function test_api_update_case(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-API-UP',
            'created_by' => $admin->id,
            'status' => MedicalCase::STATUS_DRAFT,
        ]);

        $token = $admin->createToken('admin-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->putJson("/api/cases/{$case->id}", [
                'case_number' => 'CASE-API-UP',
                'patient_reference' => $case->patient_reference,
                'surgeon_name' => $case->surgeon_name,
                'implant_type' => $case->implant_type,
                'status' => MedicalCase::STATUS_COMPLETED,
                'priority' => MedicalCase::PRIORITY_LOW,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'status' => MedicalCase::STATUS_COMPLETED,
                ],
            ]);

        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'status' => MedicalCase::STATUS_COMPLETED,
        ]);
    }

    /**
     * API Test 8: DELETE /api/cases/{id} denies Viewer with 403.
     */
    public function test_api_viewer_cannot_delete_case(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-API-VIEWER-DEL',
            'created_by' => $admin->id,
        ]);

        $viewerToken = $viewer->createToken('viewer-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$viewerToken}")
            ->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
            ]);

        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * API Test 9: DELETE /api/cases/{id} succeeds for Admin with 200.
     */
    public function test_api_admin_can_delete_case(): void
    {
        $admin = User::factory()->admin()->create();

        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-API-ADMIN-DEL',
            'created_by' => $admin->id,
        ]);

        $adminToken = $admin->createToken('admin-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->deleteJson("/api/cases/{$case->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }
}
