<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
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
            // "fileable_type" => ,
            // "fileable_id" => ,
            'type' => 'image',
            'path' => 'test.jpg',
            'size' => 1024,
            'description' => fake()->text(100),
        ];
    }
}
