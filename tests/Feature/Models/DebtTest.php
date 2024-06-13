<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DebtTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar o User
     */
    public function test_user_method(): void
    {
        User::factory(2)->create();

        $user = User::factory()->create();
        $debt = Debt::factory()->create(["user_id" => $user->id]);

        $this->assertEquals($user->id, $debt->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        User::factory(2)->create();

        $user = User::factory()->trashed()->create();
        $debt = Debt::factory()->create(["user_id" => $user->id]);

        $this->assertEquals($user->id, $debt->user->id);
    }
}
