<?php

namespace Database\Factories;

use App\Models\Assessment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Assessment>
 */
class AssessmentFactory extends Factory
{

    protected $model = Assessment::class;

    public function definition(): array
    {
        $type  = $this->faker->randomElement(['quiz', 'test', 'exam', 'assignment']);
        $score = $this->faker->numberBetween(0, 100);

        return [
            'student_id'  => Student::factory(),
            'subject_id'  => Subject::factory(),
            'name'        => ucfirst($type) . ' #' . $this->faker->numberBetween(1, 3),
            'type'        => $type,
            'score'       => $score,
            'max_score'   => 100,
            'graded_by'   => User::factory(),
            'date'        => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ];
    }
}
