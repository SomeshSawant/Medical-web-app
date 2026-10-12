<?php

namespace Tests\Feature;

use App\Models\MedicalCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MedicalCaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test 1: Valid login succeeds.
     * Requirement: 7.1
     */
    public function test_valid_login_succeeds(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/dashboard');
    }

    /**
     * Test 2: Invalid login is rejected.
     * Requirement: 7.2
     */
    public function test_invalid_login_is_rejected(): void
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test 3: A guest cannot open case pages or APIs (401 / redirect).
     * Requirement: 7.3
     */
    public function test_guest_cannot_open_case_pages_or_apis(): void
    {
        // Web requests redirect to login
        $this->get('/cases')->assertRedirect('/login');
        $this->get('/cases/create')->assertRedirect('/login');
        $this->get('/dashboard')->assertRedirect('/login');

        // JSON API requests return 401 Unauthenticated
        $this->getJson('/api/cases')
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);

        $this->postJson('/api/cases', [])
            ->assertStatus(401)
            ->assertJson([
                'success' => false,
            ]);
    }

    /**
     * Test 4: Admin can create a case.
     * Requirement: 7.4
     */
    public function test_admin_can_create_a_case(): void
    {
        $admin = User::factory()->admin()->create();

        $caseData = [
            'case_number' => 'CASE-9001',
            'patient_reference' => 'PAT-TEST01',
            'surgeon_name' => 'Dr. Gregory House',
            'implant_type' => 'Titanium Hip Stem',
            'status' => MedicalCase::STATUS_PLANNING,
            'priority' => MedicalCase::PRIORITY_HIGH,
            'surgery_date' => '2026-11-20',
        ];

        $response = $this->actingAs($admin)->post('/cases', $caseData);

        $this->assertDatabaseHas('cases', [
            'case_number' => 'CASE-9001',
            'patient_reference' => 'PAT-TEST01',
            'surgeon_name' => 'Dr. Gregory House',
            'created_by' => $admin->id,
        ]);

        $createdCase = MedicalCase::where('case_number', 'CASE-9001')->first();
        $response->assertRedirect(route('cases.show', $createdCase));
        $response->assertSessionHas('success');
    }

    /**
     * Test 5: Creating a case with a duplicate Case Number fails (422).
     * Requirement: 7.5
     */
    public function test_creating_a_case_with_a_duplicate_case_number_fails(): void
    {
        $admin = User::factory()->admin()->create();

        // Existing case
        MedicalCase::factory()->create([
            'case_number' => 'CASE-EXISTING',
            'created_by' => $admin->id,
        ]);

        // Attempt to create duplicate via JSON API to assert HTTP 422
        $response = $this->actingAs($admin)->postJson('/api/cases', [
            'case_number' => 'CASE-EXISTING',
            'patient_reference' => 'PAT-DUP01',
            'surgeon_name' => 'Dr. Test Surgeon',
            'implant_type' => 'Spinal Cage',
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_MEDIUM,
            'surgery_date' => '2026-12-01',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['case_number'])
            ->assertJsonPath('success', false);

        // Also assert web form redirects back with validation error
        $webResponse = $this->actingAs($admin)->post('/cases', [
            'case_number' => 'CASE-EXISTING',
            'patient_reference' => 'PAT-DUP02',
            'surgeon_name' => 'Dr. Test Surgeon',
            'implant_type' => 'Spinal Cage',
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_MEDIUM,
        ]);

        $webResponse->assertSessionHasErrors(['case_number']);
    }

    /**
     * Test 6: Viewer cannot delete a case (403).
     * Requirement: 7.6
     */
    public function test_viewer_cannot_delete_a_case(): void
    {
        $admin = User::factory()->admin()->create();
        $viewer = User::factory()->viewer()->create();

        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-PROTECTED',
            'created_by' => $admin->id,
        ]);

        // Viewer attempts to delete via web route
        $webResponse = $this->actingAs($viewer)->delete("/cases/{$case->id}");
        $webResponse->assertStatus(403);

        // Viewer attempts to delete via API
        $apiResponse = $this->actingAs($viewer)->deleteJson("/api/cases/{$case->id}");
        $apiResponse->assertStatus(403);

        // Verify record remains untouched in database
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'case_number' => 'CASE-PROTECTED',
        ]);
    }

    /**
     * Test 7: Viewer cannot create a case (403).
     */
    public function test_viewer_cannot_create_a_case(): void
    {
        $viewer = User::factory()->viewer()->create();

        $response = $this->actingAs($viewer)->postJson('/api/cases', [
            'case_number' => 'CASE-VIEWER-ATTEMPT',
            'patient_reference' => 'PAT-VIEWER',
            'surgeon_name' => 'Dr. Unauthorized',
            'implant_type' => 'Knee Implant',
            'status' => MedicalCase::STATUS_DRAFT,
            'priority' => MedicalCase::PRIORITY_LOW,
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('cases', ['case_number' => 'CASE-VIEWER-ATTEMPT']);
    }

    /**
     * Test 8: Admin can delete a case.
     */
    public function test_admin_can_delete_a_case(): void
    {
        $admin = User::factory()->admin()->create();
        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-TO-DELETE',
            'created_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/cases/{$case->id}");

        $response->assertRedirect(route('cases.index'));
        $this->assertDatabaseMissing('cases', ['id' => $case->id]);
    }

    /**
     * Test 9: Bonus Engineer role can create and edit cases but cannot delete.
     */
    public function test_engineer_can_create_and_edit_but_cannot_delete(): void
    {
        $engineer = User::factory()->engineer()->create();
        $case = MedicalCase::factory()->create([
            'case_number' => 'CASE-ENG-01',
            'created_by' => $engineer->id,
            'status' => MedicalCase::STATUS_PLANNING,
        ]);

        // Engineer can update case
        $updateResponse = $this->actingAs($engineer)->put("/cases/{$case->id}", [
            'case_number' => 'CASE-ENG-01',
            'patient_reference' => $case->patient_reference,
            'surgeon_name' => 'Dr. Updated Surgeon',
            'implant_type' => $case->implant_type,
            'status' => MedicalCase::STATUS_APPROVED,
            'priority' => MedicalCase::PRIORITY_HIGH,
        ]);

        $updateResponse->assertRedirect(route('cases.show', $case));
        $this->assertDatabaseHas('cases', [
            'id' => $case->id,
            'surgeon_name' => 'Dr. Updated Surgeon',
            'status' => MedicalCase::STATUS_APPROVED,
        ]);

        // Engineer cannot delete case (403)
        $deleteResponse = $this->actingAs($engineer)->delete("/cases/{$case->id}");
        $deleteResponse->assertStatus(403);
        $this->assertDatabaseHas('cases', ['id' => $case->id]);
    }

    /**
     * Test 10: Case list search and status filter return expected results.
     */
    public function test_case_list_search_and_filters(): void
    {
        $viewer = User::factory()->viewer()->create();

        MedicalCase::factory()->create([
            'case_number' => 'CASE-SEARCH-ALPHA',
            'surgeon_name' => 'Dr. UniqueSurgeon',
            'status' => MedicalCase::STATUS_PLANNING,
            'created_by' => $viewer->id,
        ]);

        MedicalCase::factory()->create([
            'case_number' => 'CASE-SEARCH-BETA',
            'surgeon_name' => 'Dr. AnotherSurgeon',
            'status' => MedicalCase::STATUS_COMPLETED,
            'created_by' => $viewer->id,
        ]);

        // Search by Surgeon
        $searchResponse = $this->actingAs($viewer)->get('/cases?search=UniqueSurgeon');
        $searchResponse->assertOk();
        $searchResponse->assertSee('CASE-SEARCH-ALPHA');
        $searchResponse->assertDontSee('CASE-SEARCH-BETA');

        // Filter by Status
        $filterResponse = $this->actingAs($viewer)->get('/cases?status=Completed');
        $filterResponse->assertOk();
        $filterResponse->assertSee('CASE-SEARCH-BETA');
        $filterResponse->assertDontSee('CASE-SEARCH-ALPHA');
    }
}
