<?php

namespace Database\Factories;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class DebtorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            "user_id" => $user,
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => fake()->word() . time() . rand(0, 1000) . rand(0, 1000),
            "amount" => 10,
            "description" => fake()->text(),
        ];
    }
}
