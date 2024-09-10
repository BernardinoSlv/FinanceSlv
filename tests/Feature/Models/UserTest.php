<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Expense;
use App\Models\File;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     */
    public function test_identifiers_method_without_identifier(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->create();

        $this->assertCount(0, $user->identifiers);
    }

    /**
     * deve retornar uma coleção com 2 Identifier
     */
    public function test_identifiers_method(): void
    {
        Identifier::factory(2)->create();

        $user = User::factory()->create();
        Identifier::factory(2)->create(['user_id' => $user]);

        $this->assertCount(2, $user->identifiers);
    }

    /**
     * deve retornar um coleção vazia
     */
    public function test_movements_method_without_movement(): void
    {
        Movement::factory(2)->create([
            'movementable_type' => Quick::class,
            'movementable_id' => Quick::factory()->create(),
        ]);

        $user = User::factory()->create();

        $this->assertCount(0, $user->movements);
    }

    /**
     * deve retornar 2 Movement
     */
    public function test_movements_method(): void
    {
        Movement::factory(2)->create([
            'movementable_type' => Quick::class,
            'movementable_id' => Quick::factory()->create(),
        ]);

        $user = User::factory()->create();
        Movement::factory(2)->create([
            'user_id' => $user,
            'movementable_type' => Quick::class,
            'movementable_id' => Quick::factory()->create(),
        ]);

        $this->assertCount(2, $user->movements);
    }

    /**
     * deve retornar uma colleção vazia
     */
    public function test_quicks_method_without_quick(): void
    {
        Quick::factory(2)->create();

        $user = User::factory()->create();

        $this->assertCount(0, $user->quicks);
    }

    /**
     * deve retornar 2 Quick
     */
    public function test_quicks_method(): void
    {
        Quick::factory(2)->create();

        $user = User::factory()->create();
        Quick::factory(2)->create(['user_id' => $user]);

        $this->assertCount(2, $user->quicks);
    }

    /**
     * deve retornar uma coleção vazia
     */
    public function test_files_method_without_file(): void
    {
        File::factory(2)->create([
            'fileable_type' => Movement::class,
            'fileable_id' => Movement::factory()->create([
                'movementable_type' => Quick::class,
                'movementable_id' => Quick::factory()->create(),
            ]),
        ]);

        $user = User::factory()->create();

        $this->assertCount(0, $user->files);
    }

    /**
     * deve retornar 2 File
     */
    public function test_files_method(): void
    {
        File::factory(2)->create([
            'fileable_type' => Movement::class,
            'fileable_id' => Movement::factory()->create([
                'movementable_type' => Quick::class,
                'movementable_id' => Quick::factory()->create(),
            ]),
        ]);

        $user = User::factory()->create();
        File::factory(2)->create([
            'user_id' => $user,
            'fileable_type' => Movement::class,
            'fileable_id' => Movement::factory()->create([
                'movementable_type' => Quick::class,
                'movementable_id' => Quick::factory()->create(),
            ]),
        ]);

        $this->assertCount(2, $user->files);
    }

    /**
     * deve retornar uma coleção vazia
     */
    public function test_debts_method_without_debts(): void
    {
        Debt::factory(2)->create();

        $user = User::factory()->create();

        $this->assertCount(0, $user->debts);
    }

    /**
     * deve retornar 2 Debt
     */
    public function test_debts_method(): void
    {
        Debt::factory(2)->create();

        $user = User::factory()->create();
        Debt::factory(2)->create(['user_id' => $user]);
        Debt::factory(2)->trashed()->create(['user_id' => $user]);

        $this->assertCount(2, $user->debts);
    }

    /**
     * deve retornar uma coleção vazia
     */
    public function test_expenses_relation_empty(): void
    {
        Expense::factory(2)->create();

        $user = User::factory()->create();

        $this->assertCount(0, $user->expenses);
    }

    /**
     * deve retornar uma coleção com 2 Expense
     */
    public function test_expenses_relation(): void
    {
        Expense::factory(2)->create();

        $user = User::factory()->create();
        $expenses = Expense::factory(2)->create([
            'user_id' => $user,
        ]);

        $this->assertInstanceOf(Expense::class, $user->expenses->first());
        $this->assertCount(
            2,
            $user->expenses->filter(
                fn (Expense $expense) => in_array($expense->id, [$expenses->get(0)->id, $expenses->get(1)->id])
            )
        );
    }
}
