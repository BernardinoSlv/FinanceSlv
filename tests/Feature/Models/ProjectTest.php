<?php

namespace Tests\Feature\Models;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    /** deve retornar o User */
    public function test_user_relation(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $project = Project::factory()->create([
            "user_id" => $user
        ]);

        $this->assertEquals($user->id, $project->user->id);
    }

    /** deve retornar uma coleção vazia */
    public function test_items_relation_without_items(): void
    {
        ProjectItem::factory(2)->create();

        $project = Project::factory()->create();

        $this->assertCount(0, $project->items);
    }

    /** deve retorn 2 ProjectItem */
    public function test_items_relation(): void
    {
        ProjectItem::factory(2)->create();

        $project = Project::factory()->create();
        ProjectItem::factory(2)->create([
            "project_id" => $project
        ]);

        $this->assertCount(2, $project->items);
    }
}
