<?php

namespace Database\Factories;

use App\Models\Queue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Queue>
 */
class QueueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $queueNumber = 'B-'.str_pad($this->faker->unique()->numberBetween(1, 100), 3, '0', STR_PAD_LEFT);

        return [
            'id' => 'Q-00'.$this->faker->unique()->numberBetween(100, 999),
            'queue_number' => $queueNumber,
            'status' => 'waiting',
            'mechanic_id' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
