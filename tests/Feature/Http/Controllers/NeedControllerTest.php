<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NeedControllerTest extends TestCase
{
    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("needs.index"))
            ->assertRedirectToRoute("auth.index");
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("needs.index"))
            ->assertOk()
            ->assertViewIs("needs.index")
            ->assertViewHas("needs");
    }
}
