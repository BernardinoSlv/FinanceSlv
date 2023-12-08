<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    /**
     * deve redirecionar para a página de login
     *
     * @return void
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("dashboard.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view dashboard.index
     *
     * @return void
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("dashboard.index"))
            ->assertOk()
            ->assertViewIs("dashboard.index");
    }
}
