<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Debt>
 */
class DebtFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "user_id" => User::factory()->create(),
            "title" => fake()->word() . time() . rand(0, 100),
            "amount" => 10,
            "description" => fake()->text(100),
            "effetive_at" => date("Y-m-d")
        ];
    }
}
