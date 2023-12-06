<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve ter status 200 e view auth.create
     *
     * @return void
     */
    public function test_create_action(): void
    {
        $this->get(route("auth.create"))
            ->assertOk()
            ->assertViewIs("auth.create");
    }
}
