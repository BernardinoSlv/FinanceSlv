<?php

namespace Tests\Feature\Repositories;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Entry;
use App\Models\Leave;
use App\Models\Movement;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\ExpenseRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BaseRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar uma coleção vazia
     *
     * @return void
     */
    public function test_all_by_user_method_nonexistent_user(): void
    {
        Entry::factory(20)->create();

        $this->assertCount(0, $this->_repository()->allByUser(0));
    }

    /**
     * deve retornar uma coleção vazia
     *
     * @return void
     */
    public function test_all_by_user_method_without_entries(): void
    {
        // entradas de outros usuários
        Entry::factory(20)->create();
        $user = User::factory()->create();

        $this->assertCount(0, $this->_repository()->allByUser($user->id));
    }

    /**
     * deve retornar uma coleção com 2 entradas
     *
     * @return void
     */
    public function test_all_by_user_method(): void
    {
        // entradas de outros usuários
        Entry::factory(20)->create();
        $user = User::factory()->create();
        Entry::factory(2)->create([
            "user_id" => $user->id
        ]);

        $this->assertCount(2, $this->_repository()->allByUser($user->id));
    }

    /**
     * deve retornar false
     */
    public function test_update_nonexistent(): void
    {
        $data = Entry::factory()->make()->toArray();

        $this->assertFalse($this->_repository()->update(0, $data));
    }

    public function test_update_method(): void
    {
        $entry = Entry::factory()->create();
        $data = Entry::factory()->make()->toArray();

        $this->assertTrue($this->_repository()->update($entry->id, $data));
        $this->assertDatabaseHas("entries", [
            ...$data,
            "id" => $entry->id,
            "user_id" => $entry->user_id
        ]);
    }

    /**
     * deve retornar false
     */
    public function test_delete_nonexistent(): void
    {
        $this->assertFalse($this->_repository()->delete(0));
    }

    /**
     * deve remover a entrada
     */
    public function test_delete(): void
    {
        $entry = Entry::factory()->create();

        $this->assertTrue($this->_repository()->delete($entry->id));
        $this->assertSoftDeleted($entry);
    }

    /**
     * deve lançar uma exceção
     */
    public function test_delete_polymorph_method_should_throw_error(): void
    {
        $this->expectException(Error::class);
        $this->expectExceptionMessage("Method is not allowed");

        app(ExpenseRepositoryContract::class)->deletePolymorph(Debtor::class, 21);
    }

    /**
     * deve retornar 0
     */
    public function test_delete_polymorph_method_without_entry(): void
    {
        Entry::factory(10)->create();
        $entries = Entry::factory(10)
            ->sequence(
                ...Debtor::factory(10)->create()->map(function (Debtor $debtor): array {
                    return ["entryable_id" => $debtor->id];
                })
            )
            ->create([
                "entryable_type" => Debtor::class
            ]);

        // dd(Entry::all()->map(fn (Entry $entry) => dump($entry->id)));
        $this->assertEquals(0, $this->_repository()->deletePolymorph(
            Debtor::class,
            $entries->last()->id + 1
        ));
        $this->assertDatabaseCount("entries", 20);
    }

    /**
     * deve retorna 1
     */
    public function test_delete_polymorph_method_with_one_entry(): void
    {
        Entry::factory(10)->create();
        Entry::factory(10)
            ->sequence(
                ...Debtor::factory(10)->create()->map(function (Debtor $debtor): array {
                    return ["entryable_id" => $debtor->id];
                })
            )
            ->create([
                "entryable_type" => Debtor::class
            ]);
        $debtor = Debtor::factory()->create();
        $entry = Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id
        ]);

        $this->assertEquals(1, $this->_repository()->deletePolymorph(Debtor::class, $debtor->id));
        $this->assertDatabaseCount("entries", 21);
        $this->assertSoftDeleted($entry);
    }

    /**
     * deve retornar 10
     */
    public function test_delete_polymorph_method(): void
    {
        Entry::factory(10)->create();
        Entry::factory(10)
            ->sequence(
                ...Debtor::factory(10)->create()->map(function (Debtor $debtor): array {
                    return ["entryable_id" => $debtor->id];
                })
            )
            ->create([
                "entryable_type" => Debtor::class
            ]);
        $debtor = Debtor::factory()->create();
        $entries = Entry::factory(5)->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id
        ]);

        $this->assertEquals(
            5,
            $this->_repository()->deletePolymorph(Debtor::class, $debtor->id)
        );
        $this->assertDatabaseCount("entries", 25);
        $this->assertSoftDeleted($entries[0]);
        $this->assertSoftDeleted($entries[1]);
        $this->assertSoftDeleted($entries[2]);
        $this->assertSoftDeleted($entries[3]);
        $this->assertSoftDeleted($entries[4]);
    }

    /**
     * deve retornar 1
     */
    public function test_delete_polymorph_method_leave_model(): void
    {
        Leave::factory(10)->create();
        Leave::factory(10)
            ->sequence(...Debt::factory(10)->create()->map(function (Debt $debt): array {
                return ["leaveable_id" => $debt->id];
            }))
            ->create([
                "leaveable_type" => Debt::class
            ]);
        $debt = Debt::factory()->create();
        $leave = Leave::factory()->create([
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt->id
        ]);

        $this->assertEquals(
            1,
            app(LeaveRepositoryContract::class)->deletePolymorph(Debt::class, $debt->id)
        );
        $this->assertDatabaseCount(Leave::class, 21);
        $this->assertSoftDeleted($leave);
    }

    /**
     * deve retornar 1
     */
    public function test_delete_polymorph_method_movement_model(): void
    {
        Movement::factory(10)->create();

        $entry = Entry::factory()->create();
        $movement = Movement::factory()->create([
            "movementable_type" => Entry::class,
            "movementable_id" => $entry->id
        ]);

        $this->assertEquals(
            1,
            app(MovementRepositoryContract::class)->deletePolymorph(Entry::class, $entry->id)
        );
        $this->assertDatabaseCount("movements", 11);
        $this->assertSoftDeleted($movement);
    }


    protected function _repository(): EntryRepositoryContract
    {
        return app()->make(EntryRepositoryContract::class);
    }
}
