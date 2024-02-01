<?php

namespace Database\Factories;

use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

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
        $user =  User::factory()->create();

        return [
            "user_id" => $user,
            "identifier_id" => Identifier::factory()->create(["user_id" => $user]),
            "title" => fake()->word() . time() . rand(0, 1000) . rand(0, 1000),
            "amount" => 10,
            // "description" => ,
            "quantity" => 10,
            "effetive_at" => date("Y-m-d"),
        ];
    }
}
