<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Testing\Fluent\AssertableJson;
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
            ->has(ProjectItem::factory(5))
            ->create();

        $this->actingAs($user)->get(route("projects.items.index", $project))
            ->assertOk()
            ->assertViewIs("projects.items.index")
            ->assertViewHas([
                "project",
                "projectItems",
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
            ]);
    }

    /** deve ter status 403 */
    public function test_store_action_is_not_own_the_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->create();
        $data = ProjectItem::factory()->make()->toArray();

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
        $data = ProjectItem::factory()->make()->toArray();

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

    /** deve ter status 422 */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()
            ->has(Project::factory()
                ->has(ProjectItem::factory(2)))
            ->create();
        $project = $user->projects->first();
        $projectItem = $project->projectItems->first();

        $this->actingAs($user)->putJson(route("projects.items.update", [
            "project" => $project,
            "projectItem" => $projectItem
        ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors([
                "name"
            ])
            ->assertJsonMissingValidationErrors([
                "description"
            ]);
    }

    /** deve ter status 403 */
    public function test_update_action_doesnt_own_from_project(): void
    {
        $user = User::factory()->create();
        $projectItem = ProjectItem::factory()->create();
        $data = ProjectItem::factory()->make()->toArray();

        $this->actingAs($user)->putJson(route("projects.items.update", [
            "project" => $projectItem->project_id,
            "projectItem" => $projectItem
        ]), $data)
            ->assertForbidden();
    }

    /** deve ter status 200 */
    public function test_update_action(): void
    {
        $user = User::factory()
            ->has(Project::factory()
                ->has(ProjectItem::factory(2)))
            ->create();
        $project = $user->projects->first();
        $projectItem = $project->projectItems->first();
        $data = ProjectItem::factory()->make(["complete" => 1])->toArray();

        $this->actingAs($user)->putJson(route("projects.items.update", [
            "project" => $project,
            "projectItem" => $projectItem
        ]), $data)
            ->assertOk()
            ->assertJson(
                fn(AssertableJson $json) => $json->has("message")
            )
            ->assertSessionHas("alert_type", "success");
        $this->assertNotNull($actualProjectItem = ProjectItem::query()->where([
            ...Arr::except($data, ["project_id"]),
            "project_id" => $project->id
        ])->first());
    }

    /** deve ter status 403 */
    public function test_destroy_action_is_not_owner(): void
    {
        $projectItem = ProjectItem::factory()->create();
        $user = User::factory()->create();

        $this->actingAs($user)->delete(route("projects.items.destroy", [
            "project" => $projectItem->project,
            "projectItem" => $projectItem
        ]))
            ->assertForbidden();
    }

    /** deve redirecionar com mensagem de sucesso */
    public function test_destroy_action(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()
            ->for($user)
            ->has(ProjectItem::factory())
            ->create();

        $this->actingAs($user)->delete(route("projects.items.destroy", [
            "project" => $project,
            "projectItem" => $project->projectItems->first()
        ]))
            ->assertRedirect(route("projects.items.index", $project))
            ->assertSessionHas("alert_type", "success");

        $this->assertDatabaseMissing("project_items", [
            "id" => $project->projectItems->first()->id
        ]);
    }
}
