<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtorControllerTest extends TestCase
{
    /**
     * deve redirecionar para página de login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route("debtors.index"))
            ->assertRedirect(route("auth.index"));
    }

    /**
     * deve ter status 200 e view debtor.index
     */
    public function test_index_action(): void
    {
        $this->actingAs($this->_user())->get(route("debtors.index"))
            ->assertOk()
            ->assertViewIs("debtor.index");
    }
}
