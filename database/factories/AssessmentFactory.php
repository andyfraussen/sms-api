<?php

namespace Database\Factories;

use App\Enums\AssessmentType;
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
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'type' => AssessmentType::cases()[array_rand(AssessmentType::cases())],
            'score' => $this->faker->randomFloat(1, 0, 100),
            'date' => $this->faker->date(),
            'comments' => $this->faker->sentence,
            'name' => $this->faker->title(),
            'graded_by' => User::factory(),
            'max_score'   => 100,
        ];
    }
}
