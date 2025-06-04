<?php

namespace Tests\Feature\Api;

use App\Enums\AssessmentType;
use App\Models\Assessment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Tests\Feature\ApiTestCase;

class AssessmentApiTest extends ApiTestCase
{
    public function test_admin_can_create_assessment(): void
    {
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = User::factory()->create();

        $payload = [
            'name' => 'Exam Subject',
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'type' => AssessmentType::EXAM->value,
            'score' => 85.5,
            'date' => now(),
            'comments' => 'Excellent performance',
            'graded_by' => $teacher->id,
        ];

        $this->actingAsRole('admin')
            ->postJson('/api/v1/assessments', $payload)
            ->assertCreated()
            ->assertJsonPath('data.score', 85.5);
    }

    public function test_teacher_can_create_assessment(): void
    {
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = User::factory()->create();

        $payload = [
            'name' => 'Exam Subject',
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'type' => AssessmentType::EXAM->value,
            'score' => 85.5,
            'date' => now(),
            'comments' => 'Excellent performance',
            'graded_by' => $teacher->id,
        ];

        $this->actingAsRole('teacher')
            ->postJson('/api/v1/assessments', $payload)
            ->assertCreated()
            ->assertJsonPath('data.score', 85.5);
    }

    public function test_student_cannot_create_assessment(): void
    {
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = User::factory()->create();

        $payload = [
            'name' => 'Exam Subject',
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'type' => AssessmentType::EXAM->value,
            'score' => 85.5,
            'date' => now(),
            'comments' => 'Excellent performance',
            'graded_by' => $teacher->id,
        ];

        $this->actingAsRole('student')
            ->postJson('/api/v1/assessments', $payload)
            ->assertForbidden();
    }

    public function test_parent_cannot_create_assessment(): void
    {
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = User::factory()->create();

        $payload = [
            'name' => 'Exam Subject',
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'type' => AssessmentType::EXAM->value,
            'score' => 85.5,
            'date' => now(),
            'comments' => 'Excellent performance',
            'graded_by' => $teacher->id,
        ];

        $this->actingAsRole('student')
            ->postJson('/api/v1/assessments', $payload)
            ->assertForbidden();
    }

    public function test_teacher_can_update_assessment(): void
    {
        $teacher = User::factory()->create();
        $assessment = Assessment::factory()->create(['graded_by' => $teacher]);
        $payload = ['score' => 69];
        $this->actingAs($teacher)
            ->patchJson("/api/v1/assessments/{$assessment->id}", $payload)
            ->assertOk()
            ->assertJsonPath('data.score', 69);
    }

    public function test_different_teacher_cannot_create_assessment(): void
    {
        $teacher = User::factory()->create();
        $assessment = Assessment::factory()->create(['graded_by' => $teacher]);
        $payload = ['score' => 69];
        $differentTeacher = User::factory()->create();

        $this->actingAs($differentTeacher)
            ->patchJson("/api/v1/assessments/{$assessment->id}", $payload)
            ->assertForbidden();
    }
}
