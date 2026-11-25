<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\NbaSarDatabaseSeeder;
use Database\Seeders\CriterionFiveDatabaseSeeder;

class Table5aFacultyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(NbaSarDatabaseSeeder::class);
        $this->seed(CriterionFiveDatabaseSeeder::class);
    }

    public function test_faculty_table_5a_endpoint_returns_enrolled_faculty_and_cadre(): void
    {
        $response = $this->getJson('/api/v1/nba/faculty-table-5a');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $data = $response->json('data');
        $this->assertArrayHasKey('faculty', $data);
        $this->assertArrayHasKey('total_count', $data);
        $this->assertArrayHasKey('cadre_distribution', $data);

        // Verify faculty count and attributes
        $this->assertGreaterThanOrEqual(60, $data['total_count']);
        $firstFaculty = $data['faculty'][0];
        $this->assertArrayHasKey('serial_no', $firstFaculty);
        $this->assertArrayHasKey('name', $firstFaculty);
        $this->assertArrayHasKey('highest_degree', $firstFaculty);
        $this->assertArrayHasKey('university', $firstFaculty);
        $this->assertArrayHasKey('specialization', $firstFaculty);
        $this->assertArrayHasKey('present_designation', $firstFaculty);
        $this->assertArrayHasKey('currently_associated', $firstFaculty);

        // Verify cadre distribution
        $cadre = $data['cadre_distribution'];
        $this->assertArrayHasKey('professors', $cadre);
        $this->assertArrayHasKey('associate_professors', $cadre);
        $this->assertArrayHasKey('assistant_professors', $cadre);
        $this->assertArrayHasKey('ratio', $cadre);
    }
}
