<?php

namespace Database\Factories;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(),
            'identifier_id' => Identifier::factory()->create(),
            'title' => fake()->word() . rand(0, 1000) . rand(0, 1000) . time(),
            'description' => fake()->text(100),
            'amount' => rand(100, 10000) / 100,
            'due_day' => rand(1, 31),
            "is_variable" => 0
        ];
    }
}
