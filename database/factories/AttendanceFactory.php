<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id'  => Student::factory(),
            'date'        => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status'      => $this->faker->randomElement(['present', 'absent', 'late']),
            'recorded_by' => User::factory(),
        ];
    }
}
