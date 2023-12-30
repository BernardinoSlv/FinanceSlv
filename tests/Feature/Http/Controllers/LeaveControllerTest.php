<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Leave;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LeaveControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_unauthenticated(): void
    {
        $this->get(route("leaves.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view leave.index
     */
    public function test_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("leaves.index"))
            ->assertOk()
            ->assertViewIs("leave.index")
            ->assertViewHas("leaves");
    }
}
