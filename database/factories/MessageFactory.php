<?php

namespace Database\Factories;

use App\Models\Message;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        // Randomly pick a Student or a User as recipient
        $recipientIsStudent = $this->faker->boolean;

        return [
            'sender_id'      => User::factory(),
            'recipient_type' => $recipientIsStudent ? Student::class : User::class,
            'recipient_id'   => $recipientIsStudent ? Student::factory() : User::factory(),
            'subject'        => $this->faker->optional()->sentence(5),
            'body'           => $this->faker->paragraphs(3, true),
            'read_at'        => $this->faker->optional(0.3)->dateTimeBetween('-1 week', 'now'),
        ];
    }
}
