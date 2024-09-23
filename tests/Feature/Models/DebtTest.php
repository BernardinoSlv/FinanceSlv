<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $debt = Debt::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $debt->user->id);
    }

    /**
     * deve retornar o User mesmo deletado
     */
    public function test_user_method_trashed_user(): void
    {
        User::factory(2)->create();

        $user = User::factory()->trashed()->create();
        $debt = Debt::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $debt->user->id);
    }

    /**
     * deve retornar uma coleção vazia
     */
    public function test_movements_method_without_movements(): void
    {
        Movement::factory(2)->for(Quick::factory(), 'movementable')->create();

        $debt = Debt::factory()->create();

        $this->assertCount(0, $debt->movements);
    }

    /**
     * deve retornar 2 Movement
     */
    public function test_movements_method(): void
    {
        Movement::factory(2)->for(Quick::factory(), 'movementable')->create();

        $debt = Debt::factory()->create();
        Movement::factory(2)->create([
            'movementable_type' => Debt::class,
            'movementable_id' => $debt,
        ]);
        Movement::factory(2)->trashed()->create([
            'movementable_type' => Debt::class,
            'movementable_id' => $debt,
        ]);

        $this->assertCount(2, $debt->movements);
    }

    /**
     * deve retornar o Identifier
     */
    public function test_indentifier_method(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->create();
        $debt = Debt::factory()->for($identifier)->create();

        $this->assertEquals($identifier->id, $debt->identifier->id);
    }

    /**
     * deve retornar o Identifier mesmo deletado
     */
    public function test_indentifier_method_trashed_identifier(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->trashed()->create();
        $debt = Debt::factory()->for($identifier)->create();

        $this->assertEquals($identifier->id, $debt->identifier->id);
    }
}
