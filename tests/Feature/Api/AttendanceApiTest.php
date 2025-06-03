<?php


namespace Tests\Feature\Api;

use App\Models\Student;
use Tests\Feature\ApiTestCase;

class AttendanceApiTest extends ApiTestCase
{
    public function test_teacher_can_mark_attendance(): void
    {
        $student = Student::factory()->create();

        $payload = [
            'student_id' => $student->id,
            'date'       => today()->toDateString(),
            'status'     => 'present',
        ];

        $this->actingAsRole('teacher')
            ->postJson('/api/v1/attendances', $payload)
            ->assertCreated();
    }

    public function test_admin_cannot_mark_attendance(): void
    {
        $student = Student::factory()->create();

        $payload = [
            'student_id' => $student->id,
            'date'       => today()->toDateString(),
            'status'     => 'present',
        ];

        $this->actingAsRole('admin')
            ->postJson('/api/v1/attendances', $payload)
            ->assertForbidden();
    }
}

