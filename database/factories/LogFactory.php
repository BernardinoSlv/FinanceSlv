<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Log>
 */
class LogFactory extends Factory
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
            "ip_address" => fake()->ipv4(),
            "loggable_type" => User::class,
            "loggable_id" => $user->id,
            "type" => "update",
            "description" => null,
            // "data" => null,
        ];
    }
}
