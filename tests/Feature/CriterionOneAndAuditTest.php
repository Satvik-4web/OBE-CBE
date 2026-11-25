<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\NbaSarDatabaseSeeder;
use Database\Seeders\CriterionFiveDatabaseSeeder;
use App\Models\AcademicAudit;

class CriterionOneAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(NbaSarDatabaseSeeder::class);
        $this->seed(CriterionFiveDatabaseSeeder::class);
    }

    public function test_criterion_one_vision_mission_in_scores_and_dashboard(): void
    {
        $response = $this->getJson('/api/v1/sar/criterion-scores');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertNotEmpty($data['criteria']);
        $c1 = collect($data['criteria'])->firstWhere('number', 1);
        $this->assertNotNull($c1);
        $this->assertStringContainsString('Vision: At TIET', $c1['summary']);
        $this->assertStringContainsString('Mission: TIET exists', $c1['summary']);

        // Check dashboard response
        $dashResponse = $this->getJson('/api/v1/nba/dashboard');
        $dashResponse->assertStatus(200);
        $dashCriteria = $dashResponse->json('data.criteria');
        $dashC1 = collect($dashCriteria)->firstWhere('number', 1);
        $this->assertNotNull($dashC1);
        $this->assertStringContainsString('Vision: At TIET', $dashC1['summary']);
    }

    public function test_nba_readiness_endpoint(): void
    {
        $response = $this->getJson('/api/v1/sar/nba-readiness');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);
        $data = $response->json('data');
        $this->assertArrayHasKey('total_score', $data);
        $this->assertArrayHasKey('tier', $data);
    }

    public function test_academic_audits_crud_workflow(): void
    {
        // 1. Create audit
        $createResponse = $this->postJson('/api/v1/academic-audits', [
            'title' => 'NBA Mock External Review 2026',
            'audit_type' => 'external',
            'status' => 'pending',
            'audit_date' => '2026-12-15',
            'findings' => 'All criteria verified.',
            'recommendations' => 'Maintain current SFR ratio.',
            'auditor_name' => 'Dr. K. Sharma',
        ]);

        $createResponse->assertStatus(201);
        $createResponse->assertJson([
            'success' => true,
            'data' => [
                'title' => 'NBA Mock External Review 2026',
                'audit_type' => 'external',
            ],
        ]);

        $auditId = $createResponse->json('data.id');

        // 2. List audits
        $listResponse = $this->getJson('/api/v1/academic-audits');
        $listResponse->assertStatus(200);
        $this->assertCount(1, $listResponse->json('data'));

        // 3. Update audit
        $updateResponse = $this->putJson("/api/v1/academic-audits/{$auditId}", [
            'title' => 'NBA Mock External Review 2026 - Finalized',
            'audit_type' => 'external',
            'status' => 'completed',
            'audit_date' => '2026-12-15',
            'findings' => 'Completed full verification.',
            'recommendations' => 'Excellent readiness.',
            'auditor_name' => 'Dr. K. Sharma',
        ]);

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson([
            'success' => true,
            'data' => [
                'status' => 'completed',
            ],
        ]);

        // 4. Delete audit
        $deleteResponse = $this->deleteJson("/api/v1/academic-audits/{$auditId}");
        $deleteResponse->assertStatus(200);
        $this->assertEquals(0, AcademicAudit::count());
    }
}
