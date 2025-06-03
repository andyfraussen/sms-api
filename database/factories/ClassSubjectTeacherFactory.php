<?php

namespace Database\Factories;

use App\Models\Pivots\ClassSubjectTeacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pivots\ClassSubjectTeacher>
 */
class ClassSubjectTeacherFactory extends Factory
{
    protected $model = ClassSubjectTeacher::class;

    public function definition(): array
    {
        $year = $this->faker->numberBetween(2020, 2025);

        return [
            'school_class_id' => SchoolClass::factory(),
            'subject_id'      => Subject::factory(),
            'teacher_id'      => User::factory(),
            'academic_year'   => $year . '-' . ($year + 1),
        ];
    }
}
