<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolClass>
 */
class SchoolClassFactory extends Factory
{
    protected $model = SchoolClass::class;

    public function definition(): array
    {
        return [
            'grade_id' => Grade::factory(),
            'name'     => sprintf('%d-%s', $this->faker->numberBetween(1, 12), $this->faker->randomLetter()),
            'room'     => $this->faker->optional()->bothify('Room ###'),
        ];
    }
}
