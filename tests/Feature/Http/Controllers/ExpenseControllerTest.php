<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_unautheticated(): void
    {
        $this->get(route("expenses.index"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view expense.index
     */
    public function test_index(): void
    {
        $this->actingAs($this->_user())->get(route("expenses.index"))
            ->assertOk()
            ->assertViewIs("expense.index")
            ->assertViewHas("expenses");
    }

    /**
     * deve redirecionar para página de login
     */
    public function test_create_unathenticated(): void
    {
        $this->get(route("expenses.create"))->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view expense.create
     */
    public function test_create(): void
    {
        $this->actingAs($this->_user())->get(route("expenses.create"))
            ->assertOk()
            ->assertViewIs("expense.create");
    }
}
