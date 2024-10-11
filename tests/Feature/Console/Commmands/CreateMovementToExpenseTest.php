<?php

namespace Tests\Feature\Console\Commmands;

use App\Models\Expense;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMovementToExpenseTest extends TestCase
{
    use RefreshDatabase;

    /** não deve adicionar movimentação nova */
    public function test_handle_method_without_available_expense_to_create_movement(): void
    {
        $this->travelTo(now()->day(10));

        Expense::factory(2)->create([
            'due_day' => 1,
        ]);

        $this->artisan('app:cmte');

        $this->assertDatabaseCount('movements', 0);
    }

    /** deve criar 2 novas movimentações */
    public function test_handle_method_with_available_expense_to_create_movement(): void
    {
        $this->travelTo(now()->day(10));

        $expenses = Expense::factory(2)->create([
            'due_day' => 11,
            'amount' => 500,
        ]);

        $this->artisan('app:cmte');

        $this->assertCount(
            2,
            Movement::query()->where([
                'movementable_type' => Expense::class,
                'type' => 'out',
                'effetive_date' => now()->day(11)->format('Y-m-d'),
                'amount' => 500,
            ])
                ->whereIn('user_id', [$expenses->get(0)->user_id, $expenses->get(1)->user_id])
                ->get()
        );
    }

    public function test_handle_method_with_available_expense_but_have_movement_to_this_month(): void
    {
        $this->travelTo(now()->day(10));

        $expenses = Expense::factory(2)
            ->has(Movement::factory()
                ->state([
                    'closed_date' => now()->subDay(),
                    'fees_amount' => 0,
                    'effetive_date' => now()->subDay(),
                    'type' => 'out',
                    'amount' => 500,
                ]))
            ->create([
                'due_day' => 11,
                'amount' => 500,
            ]);
        foreach ($expenses as $expense) {
            $movement = $expense->movements->first();
            $movement->user_id = $expense->user_id;
            $movement->identifier_id = $expense->identifier_id;
            $movement->save();
        }

        $this->artisan('app:cmte');

        $this->assertDatabaseCount('movements', 2);
        $this->assertCount(
            2,
            Movement::query()->where([
                'movementable_type' => Expense::class,
                'type' => 'out',
                'effetive_date' => now()->subDay()->format('Y-m-d'),
                'amount' => 500,
            ])
                ->whereIn('user_id', [$expenses->get(0)->user_id, $expenses->get(1)->user_id])
                ->get()
        );
    }

    public function test_handle_method_with_available_expense_but_trashed_movement_to_this_month(): void
    {
        $this->travelTo(now()->day(10));

        $expenses = Expense::factory(2)
            ->has(Movement::factory()
                ->trashed()
                ->state([
                    'closed_date' => now()->subDay(),
                    'fees_amount' => 0,
                    'effetive_date' => now()->subDay(),
                    'type' => 'out',
                    'amount' => 500,
                ]))
            ->create([
                'due_day' => 11,
                'amount' => 500,
            ]);
        foreach ($expenses as $expense) {
            $movement = $expense->movements()->withTrashed()->first();
            $movement->user_id = $expense->user_id;
            $movement->identifier_id = $expense->identifier_id;
            $movement->save();
        }

        $this->artisan('app:cmte');

        $this->assertDatabaseCount('movements', 2);
        $this->assertCount(
            2,
            Movement::withTrashed()->where([
                'movementable_type' => Expense::class,
                'type' => 'out',
                'effetive_date' => now()->subDay()->format('Y-m-d'),
                'amount' => 500,
            ])
                ->whereIn('user_id', [$expenses->get(0)->user_id, $expenses->get(1)->user_id])
                ->get()
        );
    }
}
