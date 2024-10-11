<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $user = User::factory()->create();
    }
}
