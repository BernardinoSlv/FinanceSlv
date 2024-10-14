<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectItemControllerTest extends TestCase
{
    use RefreshDatabase;

    /** deve redirecionar para login  */
    public function test_index_action_unauthenticated(): void
    {
        $project = Project::factory()->create();

        $this->get(route("projects.items.index", $project))
            ->assertRedirect(route("auth.index"));
    }

    /** deve ter status 403 */
    public function test_index_action_is_not_owner_the_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->create();

        $this->actingAs($user)->get(route("projects.items.index", $project))
            ->assertForbidden();
    }

    /** deve ter status 200 */
    public function test_index_action(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)
            ->has(ProjectItem::factory(5), "items")
            ->create();

        $this->actingAs($user)->get(route("projects.items.index", $project))
            ->assertOk()
            ->assertViewIs("projects.items.index")
            ->assertViewHas([
                "project",
                "projectItems",
                "identifiers"
            ]);
    }
}
