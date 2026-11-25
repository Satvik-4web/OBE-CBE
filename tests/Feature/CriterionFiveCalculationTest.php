<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\NbaSarDatabaseSeeder;
use Database\Seeders\CriterionFiveDatabaseSeeder;
use App\Domains\Academic\Models\Program;
use App\Domains\Faculty\Services\CriterionFiveService;

class CriterionFiveCalculationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(NbaSarDatabaseSeeder::class);
        $this->seed(CriterionFiveDatabaseSeeder::class);
    }

    public function test_criterion_five_service_computes_all_subcriteria(): void
    {
        $program = Program::first();
        $this->assertNotNull($program);

        $service = new CriterionFiveService();
        $result = $service->calculateFullCriterion($program);

        $this->assertEquals(100.0, $result['total_max_marks']);
        $this->assertArrayHasKey('c51', $result['sub_criteria']);
        $this->assertArrayHasKey('c52', $result['sub_criteria']);
        $this->assertArrayHasKey('c53', $result['sub_criteria']);
        $this->assertArrayHasKey('c54', $result['sub_criteria']);
        $this->assertArrayHasKey('c55', $result['sub_criteria']);

        // Check scores match reference document calculations
        $this->assertEquals(30.0, $result['sub_criteria']['c51']['max_marks']);
        $this->assertEquals(18.0, $result['sub_criteria']['c51']['claimed_marks']);

        $this->assertEquals(25.0, $result['sub_criteria']['c52']['max_marks']);
        $this->assertEquals(22.12, $result['sub_criteria']['c52']['claimed_marks']);

        $this->assertEquals(25.0, $result['sub_criteria']['c53']['max_marks']);
        $this->assertEquals(20.69, $result['sub_criteria']['c53']['claimed_marks']);

        $this->assertEquals(10.0, $result['sub_criteria']['c54']['max_marks']);
        $this->assertEquals(10.0, $result['sub_criteria']['c54']['claimed_marks']);

        $this->assertEquals(10.0, $result['sub_criteria']['c55']['max_marks']);
        $this->assertEquals(7.83, $result['sub_criteria']['c55']['claimed_marks']);

        $this->assertEquals(78.64, $result['total_claimed_marks']);
    }

    public function test_criterion_five_api_endpoints(): void
    {
        $response = $this->getJson('/api/v1/sar/criterion-5');
        $response->assertStatus(200);
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('data.total_max_marks', 100);

        $facultyResponse = $this->getJson('/api/v1/sar/criterion-5/faculty');
        $facultyResponse->assertStatus(200);
        $facultyResponse->assertJsonPath('success', true);
    }
}
