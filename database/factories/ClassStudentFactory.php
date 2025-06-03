<?php

namespace Database\Factories;

use App\Models\Pivots\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pivots\ClassStudent>
 */
class ClassStudentFactory extends Factory
{
    protected $model = ClassStudent::class;

    public function definition(): array
    {
        $from = $this->faker->dateTimeBetween('-3 years', '-1 year');
        $to   = $this->faker->optional(0.3)->dateTimeBetween($from, 'now');

        return [
            'school_class_id' => SchoolClass::factory(),
            'student_id'      => Student::factory(),
            'enrolled_from'   => $from->format('Y-m-d'),
            'enrolled_to'     => $to?->format('Y-m-d'),
        ];
    }
}
