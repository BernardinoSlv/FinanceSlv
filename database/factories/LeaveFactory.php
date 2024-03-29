<?php

namespace Database\Factories;

use App\Models\Debt;
use App\Models\Identifier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Leave>
 */
class LeaveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        $debt = Debt::factory()->create([
            "user_id" => $user,
            "identifier_id" => Identifier::factory()->create([
                "user_id" => $user
            ])
        ]);

        return [
            "user_id" => $user,
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt,
            "amount" => 200
        ];
    }
}
