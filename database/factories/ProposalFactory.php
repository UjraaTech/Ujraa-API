<?php

namespace Database\Factories;

use App\Models\Proposal;
use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProposalFactory extends Factory
{
    protected $model = Proposal::class;

    public function definition()
    {
        return [
            'job_id' => Job::factory(),
            'freelancer_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 5000),
            'delivery_days' => $this->faker->numberBetween(1, 30),
            'cover_letter' => $this->faker->paragraphs(2, true),
            'status' => 'pending',
            'credits_used' => $this->faker->randomElement([2, 4, 6])
        ];
    }

    public function accepted()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'accepted'
            ];
        });
    }

    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected'
            ];
        });
    }
}