<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition()
    {
        return [
            'client_id' => User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraphs(3, true),
            'budget' => $this->faker->randomFloat(2, 100, 5000),
            'status' => $this->faker->randomElement(['draft', 'open', 'in_progress', 'completed', 'cancelled']),
            'proposal_count' => $this->faker->numberBetween(0, 50),
            'deadline' => $this->faker->dateTimeBetween('+1 week', '+1 month')
        ];
    }

    public function draft()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'draft'
            ];
        });
    }

    public function open()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'open'
            ];
        });
    }
}