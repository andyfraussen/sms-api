<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Subject>
 */
class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    private array $subjects = [
        'Mathematics', 'English', 'Science', 'History', 'Geography',
        'Computer Studies', 'Art', 'Physical Education',
    ];

    public function definition(): array
    {
        $name = $this->faker->unique()->randomElement($this->subjects);

        return [
            'school_id' => School::factory(),
            'name'      => $name,
            'code'      => strtoupper(substr($name, 0, 4)) . '-' . $this->faker->unique()->numberBetween(1, 50),
        ];
    }
}
