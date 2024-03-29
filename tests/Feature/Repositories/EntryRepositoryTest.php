<?php

namespace Tests\Feature\Repositories;

use App\Models\Debtor;
use App\Models\Entry;
use App\Models\Expense;
use App\Models\User;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class EntryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve retornar apenas as entradas do mês atual
     *
     * @return void
     */
    public function test_all_by_user_passing_only_current_month_true(): void
    {
        Entry::factory(20)->create();
        $user = User::factory()->create();

        // do usuário , do mês anterior
        Entry::factory(10)->create([
            "user_id" => $user->id,
            "created_at" => date("Y-m-d", strtotime("-31 days"))
        ]);
        Entry::factory(2)->create([
            "user_id" => $user->id
        ]);

        $this->assertCount(2, $this->_repository()->allByUser($user->id, true));
    }

    /**
     * deve criar registro
     */
    public function test_create_method(): void
    {
        $debtor = Debtor::factory()->create();
        $data = Entry::factory()->make([
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id,
            "user_id" => $debtor->user_id
        ])->toArray();

        $this->_repository()->create($debtor->user_id, $data);

        $this->assertDatabaseHas("entries", [
            ...$data,
        ]);
    }


    protected function _repository(): EntryRepositoryContract
    {
        return App::make(EntryRepositoryContract::class);
    }
}
