<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
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

    /** deve ter status 422 */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user)->create();

        $this->actingAs($user)->postJson(route("projects.items.store", $project))
            ->assertJsonValidationErrors([
                "name",
            ])
            ->assertJsonMissingValidationErrors([
                "identifier_id",
                "amount",
                "description"
            ]);
    }

    /** deve ter status 422 */
    public function test_store_action_duplicated_name(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($user)
            ->has(ProjectItem::factory(2), "items")
            ->create();
        $data = ProjectItem::factory()->make([
            "name" => $project->items->get(0)->name,
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.items.store", $project), $data)
            ->assertJsonValidationErrors([
                "name",
            ])
            ->assertJsonMissingValidationErrors([
                "identifier_id",
                "amount",
                "description"
            ]);
    }

    /** deve ter status 403 */
    public function test_store_action_is_not_own_the_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->create();
        $data = ProjectItem::factory()->make([
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.items.store", $project), $data)
            ->assertForbidden();
    }

    /** deve ter status 201 */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($user)
            ->create();
        $data = ProjectItem::factory()->make([
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.items.store", $project), $data)
            ->assertCreated()
            ->assertJson([
                "message" => "Item criado."
            ])
            ->assertSessionHas("alert_type", "success");
        $this->assertNotNull($projectItem = ProjectItem::query()->where([
            ...Arr::except($data, ["amount", "project_id"]),
            "project_id" => $project->id,
        ])->first());
    }

    /** deve ter status 201 */
    public function test_store_action_same_name_that_another_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($user)
            ->create();
        $data = ProjectItem::factory()->make([
            "name" => ProjectItem::factory()->create()->name,
            "amount" => "500,00"
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.items.store", $project), $data)
            ->assertCreated()
            ->assertJson([
                "message" => "Item criado."
            ])
            ->assertSessionHas("alert_type", "success");
    }
}
