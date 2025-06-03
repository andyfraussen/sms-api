<?php

namespace Database\Factories;

use App\Enums\Gender;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'school_class_id' => SchoolClass::factory(),
            'registration_no' => strtoupper(Str::random(10)),
            'first_name'      => $this->faker->firstName,
            'last_name'       => $this->faker->lastName,
            'date_of_birth'   => $this->faker->dateTimeBetween('-20 years', '-5 years')->format('Y-m-d'),
            'gender'          => $this->faker->randomElement(Gender::cases())->value,
        ];
    }
}
