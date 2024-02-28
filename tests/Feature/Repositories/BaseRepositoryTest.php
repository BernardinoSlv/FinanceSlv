<?php

namespace Tests\Feature\Repositories;

use App\Models\Debtor;
use App\Models\Entry;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\ExpenseRepositoryContract;
use Error;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
        $this->assertDatabaseMissing("entries", [
            "id" => $entry->id
        ]);
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
        Entry::factory()->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id
        ]);

        $this->assertEquals(1, $this->_repository()->deletePolymorph(Debtor::class, $debtor->id));
        $this->assertDatabaseCount("entries", 20);
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
        Entry::factory(5)->create([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id
        ]);

        $this->assertEquals(5, $this->_repository()->deletePolymorph(Debtor::class, $debtor->id));
        $this->assertDatabaseCount("entries", 20);
    }


    protected function _repository(): EntryRepositoryContract
    {
        return app()->make(EntryRepositoryContract::class);
    }
}
