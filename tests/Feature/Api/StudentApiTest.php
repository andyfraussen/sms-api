<?php


namespace Tests\Feature\Api;

use App\Models\Student;
use App\Models\User;
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

    public function test_admin_can_update_student(): void
    {
        $student = Student::factory()->create();
        $payload = ['first_name' => 'Edited'];

        $this->actingAsRole('admin')
            ->patchJson("/api/v1/students/{$student->id}", $payload)
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Edited');
    }

    public function test_parent_cannot_update_student(): void
    {
        $student = Student::factory()->create();

        $this->actingAsRole('parent')
            ->patchJson("/api/v1/students/{$student->id}", ['first_name' => 'x'])
            ->assertForbidden();
    }

    public function test_student_cannot_update_student(): void
    {
        $student = Student::factory()->create();

        $this->actingAsRole('student')
            ->patchJson("/api/v1/students/{$student->id}", ['first_name' => 'x'])
            ->assertForbidden();
    }

    public function test_admin_can_soft_delete_student(): void
    {
        $student = Student::factory()->create();

        $this->actingAsRole('admin')
            ->deleteJson(route('students.delete', $student))
            ->assertNoContent();

        $this->assertSoftDeleted('students', ['id' => $student->id]);
    }

    public function test_admin_can_delete_student_permanently(): void
    {
        $student = Student::factory()->create();

        $this->actingAsRole('admin')
            ->deleteJson(route('students.destroy', $student))
            ->assertNoContent();

        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    public function test_admin_can_list_trashed_students(): void
    {
        $trashed = Student::factory()->count(2)->create();
        Student::destroy($trashed->pluck('id'));

        $this->actingAsRole('admin')
            ->getJson(route('students.trashed'))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_non_admin_cannot_list_trashed_students(): void
    {
        $this->actingAsRole('parent')
            ->getJson(route('students.trashed'))
            ->assertForbidden();
    }

    public function test_admin_can_restore_a_student(): void
    {
        $student = Student::factory()->create();
        $student->delete();

        $this->actingAsRole('admin')
            ->postJson(route('students.restore', $student))
            ->assertOk()
            ->assertJsonPath('data.id', $student->id);

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'deleted_at' => null,
        ]);
    }

    public function test_non_admin_cannot_restore_student(): void
    {
        $student = Student::factory()->create();
        $student->delete();

        $this->actingAsRole('student')
            ->postJson(route('students.restore', $student))
            ->assertForbidden();
    }
}
