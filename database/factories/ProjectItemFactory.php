<?php

namespace Database\Factories;

use App\Models\Movement;
use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjectItem>
 */
class ProjectItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "project_id" => Project::factory()->create(),
            "name" => fake()->word() . time() . rand(0, 1000),
            "debt_id" => null,
            "amount" => rand(10, 100),
            "complete" => 0,
            "description" => fake()->text(100)
        ];
    }
}
