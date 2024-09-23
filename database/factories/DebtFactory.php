<?php

namespace Database\Factories;

use App\Models\Identifier;
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
            'user_id' => User::factory()->create(),
            'identifier_id' => Identifier::factory()->create(),
            'title' => fake()->words(3, true),
            'amount' => rand(0, 1000),
            'description' => fake()->text(100),
            'installments' => null,
            'due_date' => null,
        ];
    }
}
