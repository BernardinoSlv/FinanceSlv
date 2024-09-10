<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Expense;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * deve redirecionar para login
     */
    public function test_index_action_unauthenticated(): void
    {
        $this->get(route('expenses.index'))
            ->assertRedirect(route('auth.index'));
    }

    /**
     * deve ter status 200
     */
    public function test_index_action(): void
    {
        $user = User::factory()
            ->has(Expense::factory(2))
            ->create();

        $this->actingAs($user)->get(route('expenses.index'))
            ->assertOk()
            ->assertViewHas('expenses', function (LengthAwarePaginator $expenses): bool {
                return $expenses->total() === 2;
            })
            ->assertViewIs('expenses.index');
    }

    /**deve ter status 200 */
    public function test_create_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('expenses.create'))
            ->assertOk()
            ->assertViewIs('expenses.create')
            ->assertViewHas(['identifiers']);
    }

    /**
     * ceve redirecionar com erros de validação
     */
    public function test_store_action_without_data(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post(route('expenses.store'))
            ->assertFound()
            ->assertSessionHasErrors([
                'identifier_id',
                'title',
                'amount',
                'due_day',
            ])
            ->assertSessionDoesntHaveErrors([
                'description',
            ]);
    }

    /**
     * deve redirecionar apenas com erro no campo identifier_id
     */
    public function test_store_action_is_not_owner_from_the_identifier(): void
    {
        $user = User::factory()->create();
        $data = Expense::factory()->make([
            'amount' => '200,00',
        ])->toArray();

        $this->actingAs($user)->post(route('expenses.store'), $data)
            ->assertFound()
            ->assertSessionHasErrors([
                'identifier_id',

            ])
            ->assertSessionDoesntHaveErrors([
                'title',
                'amount',
                'due_day',
                'description',
            ]);
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_store_action(): void
    {
        // travando o dia no meio do mês pois será colocado due_day antes do dia
        $this->travelTo(now()->day(15));

        $user = User::factory()
            ->has(Identifier::factory())->create();
        $data = Expense::factory()->make([
            'amount' => '200,00',
            'identifier_id' => $user->identifiers->get(0),
            'due_day' => 1,
        ])->toArray();

        $this->actingAs($user)->post(route('expenses.store'), $data)
            ->assertRedirect(route('expenses.index'))
            ->assertSessionHas('alert_type', 'success');
        $this->assertNotNull($expense = Expense::query()->where([
            ...Arr::except($data, 'user_id'),
            'amount' => 200,
        ])->first());
        $this->assertCount(0, $expense->movements);
    }

    /**
     * deve criar uma movimentação de despesa
     */
    public function test_store_action_before_the_due_day(): void
    {
        // travando o dia no início do mês
        $this->travelTo(now()->day(1));

        $user = User::factory()
            ->has(Identifier::factory())->create();
        $data = Expense::factory()->make([
            'amount' => '200,00',
            'identifier_id' => $user->identifiers->get(0),
            'due_day' => 10,
        ])->toArray();
        $this->actingAs($user)->post(route('expenses.store'), $data)
            ->assertRedirect(route('expenses.index'))
            ->assertSessionHas('alert_type', 'success');
        $expense = Expense::query()->where([
            ...Arr::except($data, 'user_id'),
            'amount' => 200,
        ])->first();
        $this->assertCount(1, $expense->movements()->where([
            'identifier_id' => $data['identifier_id'],
            'effetive_date' => now()->day(10)->format('Y-m-d'),
            'closed_date' => null,
            'fees_amount' => 0,
            'type' => 'out',
        ])->get());
    }

    /**
     * deve ter status 403
     */
    public function test_edit_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create();

        $this->actingAs($user)->get(route('expenses.edit', $expense))
            ->assertForbidden();
    }

    /**
     * deve ter status 200
     */
    public function test_edit_action(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->for($user)->create();

        $this->actingAs($user)->get(route('expenses.edit', $expense))
            ->assertOk()
            ->assertViewHas(['identifiers', 'expense'])
            ->assertViewIs('expenses.edit');
    }

    /** deve redirecionar com erros de validação  */
    public function test_update_action_without_data(): void
    {
        $user = User::factory()
            ->has(Expense::factory())
            ->create();
        $expense = $user->expenses->first();

        $this->actingAs($user)->put(route('expenses.update', $expense))
            ->assertFound()
            ->assertSessionHasErrors([
                'identifier_id',
                'title',
                'amount',
                'due_day',
            ])
            ->assertSessionDoesntHaveErrors([
                'description',
            ]);
    }

    /**
     * deve ter status 403
     */
    public function test_update_action_is_not_owner(): void
    {
        $user = User::factory()
            ->has(Identifier::factory())
            ->create();
        $expense = Expense::factory()->create();
        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00 ',
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_update_action(): void
    {
        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory()
                    ->has(Movement::factory(2, [
                        'effetive_date' => now(),
                    ]))
            )
            ->create();
        $expense = $user->expenses->first();
        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => now()->day,
        ])->toArray();

        $expense->refresh();
        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertRedirect(route('expenses.edit', $expense))
            ->assertSessionHas('alert_type', 'success');
        $this->assertDatabaseHas('expenses', [
            ...Arr::except($data, ['user_id']),
            'amount' => 500.00,
        ]);
        $this->assertCount(2, $expense->movements()->where('identifier_id', $data['identifier_id'])->get());
    }

    /**
     * deve alterar o dia e criar um movimentação
     */
    public function test_update_action_due_day_to_later_and_not_stored_monthly_movement(): void
    {
        // travando no início do mês
        $this->travelTo(now()->day(1));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(Expense::factory(state: ['due_day' => 1]))
            ->create();
        $expense = $user->expenses->first();
        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 10, // 10 dias do due_day a da data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $this->assertCount(1, $expense->movements);
        $this->assertCount(1, $expense->movements()->where([
            'identifier_id' => $data['identifier_id'],
            'user_id' => $user->id,
            'type' => 'out',
            'amount' => 500.00,
            'effetive_date' => now()->day(10)->format('Y-m-d'),
            'closed_date' => null,
            'fees_amount' => 0.00,
        ])->get());
    }

    /**
     * deve alterar o dia e não deve criar outra movimentação
     */
    public function test_update_action_due_day_to_later_and_already_stored_monthly_movement(): void
    {
        // travando no dia 10 do mês
        $this->travelTo(now()->day(10));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory(state: ['due_day' => 1])
                    ->has(Movement::factory()
                        ->state([
                            'closed_date' => now()->subDay(),
                            'fees_amount' => 0,
                            'effetive_date' => now()->subDay(),
                            'type' => 'out',
                        ]))
            )
            ->create();

        $expense = $user->expenses->first();
        $movement = $expense->movements->first();

        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 20, // 20 dias a mais da due_day a do data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertCount(1, $expense->movements);
        $this->assertEquals($movement->id, $expense->movements->first()->id);
    }

    /**
     * deve alterar o dia e não deve criar outra movimentação
     */
    public function test_update_action_due_day_to_later_and_trashed_stored_monthly_movement(): void
    {
        // travando no dia 10 do mês
        $this->travelTo(now()->day(10));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory(state: ['due_day' => 1])
                    ->has(
                        Movement::factory()
                            ->state([
                                'closed_date' => now()->subDay(),
                                'fees_amount' => 0,
                                'effetive_date' => now()->subDay(),
                                'type' => 'out',
                            ])->trashed()
                    )
            )
            ->create();

        $expense = $user->expenses->first();
        $movement = $expense->movements()->withTrashed()->first();

        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 20, // 20 dias a mais da due_day a do data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertCount(1, $expense->movements()->withTrashed()->get());
        $this->assertEquals($movement->id, $expense->movements()->withTrashed()->first()->id);
    }

    /**
     * deve mudar sem adicionar movimentão
     */
    public function test_update_action_due_day_to_previous_but_not_previous_current_date(): void
    {
        // travando no dia 10 do mês
        $this->travelTo(now()->day(20));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory(state: ['due_day' => 1])
                    ->has(Movement::factory()
                        ->state([
                            'closed_date' => now()->day(25),
                            'fees_amount' => 0,
                            'effetive_date' => now()->day(25),
                            'type' => 'out',
                        ]))
            )
            ->create();

        $expense = $user->expenses->first();
        $movement = $expense->movements->first();

        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 23, // 2 dias antes , e 3 dias após a data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertCount(1, $expense->movements);
        $this->assertEquals($movement->id, $expense->movements->first()->id);
    }

    /** deve alterar sem adicionar movimentação  */
    public function test_update_action_due_day_to_previous_also_current_date(): void
    {
        // travando no dia 10 do mês
        $this->travelTo(now()->day(20));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory(state: ['due_day' => 1])
                    ->has(Movement::factory()
                        ->state([
                            'closed_date' => now()->day(25),
                            'fees_amount' => 0,
                            'effetive_date' => now()->day(25),
                            'type' => 'out',
                        ]))
            )
            ->create();

        $expense = $user->expenses->first();
        $movement = $expense->movements->first();

        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 15, // 10 dias antes , e 5 dias após a data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertCount(1, $expense->movements);
        $this->assertEquals($movement->id, $expense->movements->first()->id);
    }

    /** deve alterar sem adicionar movimentação  */
    public function test_update_action_due_day_to_previous_also_current_date_and_not_movement_in_current_monthly(): void
    {
        // travando no dia 10 do mês
        $this->travelTo(now()->day(20));

        $user = User::factory()
            ->has(Identifier::factory())
            ->has(
                Expense::factory(state: ['due_day' => 1])
                    ->has(Movement::factory()
                        ->state([
                            'closed_date' => now()->subMonth(),
                            'fees_amount' => 0,
                            'effetive_date' => now()->subMonth(),
                            'type' => 'out',
                        ]))
            )
            ->create();

        $expense = $user->expenses->first();
        $movement = $expense->movements->first();

        $data = Expense::factory()->make([
            'identifier_id' => $user->identifiers->first(),
            'amount' => '500,00',
            'due_day' => 15, // 10 dias antes , e 5 dias após a data atual
        ])->toArray();

        $this->actingAs($user)->put(route('expenses.update', $expense), $data)
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertCount(2, $expense->movements);
        $this->assertCount(
            1,
            $expense->movements()
                ->where([
                    'effetive_date' => now()->day(15)->format('Y-m-d'),
                    'amount' => 500,
                    'closed_date' => null,
                ])
                ->get()
        );
    }

    /**
     * deve ter stauts 403
     */
    public function test_destroy_action_is_not_owner(): void
    {
        $user = User::factory()->create();
        $expense = Expense::factory()->create();

        $this->actingAs($user)->delete(route('expenses.destroy', $expense))
            ->assertForbidden();
    }

    /**
     * deve redirecionar com mensagem de sucesso
     */
    public function test_destroy_action(): void
    {
        $user = User::factory()
            ->has(Expense::factory())
            ->create();
        $expense = $user->expenses->first();

        $this->actingAs($user)->delete(route('expenses.destroy', $expense))
            ->assertRedirect(route('expenses.index'))
            ->assertSessionHas('alert_type', 'success');
        $this->assertSoftDeleted($expense);
    }

    /**
     * deve deletar a despesa e deixar as movimentações
     */
    public function test_destroy_action_has_movements(): void
    {
        $user = User::factory()
            ->has(Expense::factory()
                ->has(Movement::factory(2)))
            ->create();
        $expense = $user->expenses->first();

        $this->actingAs($user)->delete(route('expenses.destroy', $expense))
            ->assertRedirect(route('expenses.index'))
            ->assertSessionHas('alert_type', 'success');
        $expense->refresh();
        $this->assertSoftDeleted($expense);
        $this->assertCount(2, $expense->movements);
    }
}
