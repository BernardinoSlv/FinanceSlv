<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para página de login
     *
     * @return void
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("entry.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view entry.index
     *
     * @return void
     */
    public function test_index_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route("entry.index"))
            ->assertOk()
            ->assertViewIs("entry.index")
            ->assertViewHas("entries");
    }
}
