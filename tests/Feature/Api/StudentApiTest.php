<?php


namespace Tests\Feature\Api;

use App\Models\Student;
use Tests\Feature\ApiTestCase;

class StudentApiTest extends ApiTestCase
{
    public function test_admin_can_create_student(): void
    {
        $payload = Student::factory()->make()->toArray();

        $this->actingAsRole('admin')
            ->postJson('/api/v1/students', $payload)
            ->assertCreated()
            ->assertJsonPath('data.registration_no', $payload['registration_no']);
    }

    public function test_parent_cannot_create_student(): void
    {
        $payload = Student::factory()->make()->toArray();

        $this->actingAsRole('parent')
            ->postJson('/api/v1/students', $payload)
            ->assertForbidden();
    }
}
