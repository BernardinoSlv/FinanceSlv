<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ProjectControllerTest extends TestCase
{
    use RefreshDatabase;

    /** deve redirecionar para login */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("projects.index"))
            ->assertRedirect(route("auth.index"));
    }

    /** deve ter status 200 */
    public function test_index_action(): void
    {
        Project::factory(2)->create();
        $user = User::factory()
            ->has(Project::factory(3))
            ->create();

        $this->actingAs($user)->get(route("projects.index"))
            ->assertOk()
            ->assertViewIs("projects.index")
            ->assertViewHas([
                "projects"
            ]);
    }

    /** deve ter status 422 */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route("projects.store"))
            ->assertUnprocessable()
            ->assertJsonValidationErrors("name")
            ->assertJsonMissingValidationErrors("description");
    }

    /** deve ter status 422 */
    public function test_store_action_duplicate_name(): void
    {
        $user = User::factory()
            ->has(Project::factory())
            ->create();
        $data = Project::factory()->make([
            "name" => $user->projects->first()->name,
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.store"), $data)
            ->assertJsonValidationErrors("name")
            ->assertJsonMissingValidationErrors("description");
    }

    /** deve ter status 201 */
    public function test_store_action(): void
    {
        $user = User::factory()->create();
        $data = Project::factory()->make()->toArray();

        $this->actingAs($user)->postJson(route("projects.store"), $data)
            ->assertCreated()
            ->assertJson([
                "message" => "Projeto criado com sucesso."
            ])
            ->assertSessionHas("alert_type", "success");
        $this->assertNotNull($project = Project::query()->where([
            ...Arr::only($data, ["name", "description"]),
            "user_id" => $user->id
        ])->first());
    }

    public function test_store_action_same_name_that_other_user(): void
    {
        $user = User::factory()->create();
        $data = Project::factory()->make([
            "name" => Project::factory()->create()->name
        ])->toArray();

        $this->actingAs($user)->postJson(route("projects.store"), $data)
            ->assertCreated()
            ->assertJson([
                "message" => "Projeto criado com sucesso."
            ]);
        $this->assertNotNull($project = Project::query()->where([
            ...Arr::only($data, ["name", "description"]),
            "user_id" => $user->id
        ])->first());
    }
}
