<?php

namespace Tests\Feature\Console\Commmands;

use App\Models\Debt;
use App\Models\Movement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateMovementToDebtTest extends TestCase
{
    use RefreshDatabase;

    /** não deve adicionar movimentação nova */
    public function test_handle_method_without_available_debt_to_create_movement(): void
    {
        $this->travelTo(now()->day(10));

        Debt::factory(2)->create([
            "installments" => 10,
            'due_date' => now()->day(9)->format("Y-m-d"),
        ]);

        $this->artisan('app:cmtd');

        $this->assertDatabaseCount('movements', 0);
    }

    /** deve criar 2 novas movimentações */
    public function test_handle_method_with_available_debt_to_create_movement(): void
    {
        $this->travelTo(now()->day(10));

        $debts = Debt::factory(2)->create([
            "installments" => 10,
            'due_date' => now()->day(15)->format("Y-m-d"),
            'amount' => 500,
        ]);

        $this->artisan('app:cmtd');

        $this->assertCount(
            2,
            Movement::query()->where([
                'movementable_type' => Debt::class,
                'type' => 'out',
                'effetive_date' => now()->day(15)->format('Y-m-d'),
                'amount' => 50,
            ])
                ->whereIn('user_id', [$debts->get(0)->user_id, $debts->get(1)->user_id])
                ->get()
        );
    }

    public function test_handle_method_with_available_debt_but_have_movement_to_this_month(): void
    {
        $this->travelTo(now()->day(10));

        $debts = Debt::factory(2)
            ->has(Movement::factory()
                ->state([
                    'closed_date' => now()->subDay(),
                    'fees_amount' => 0,
                    'effetive_date' => now()->subDay(),
                    'type' => 'out',
                    'amount' => 50,
                ]))
            ->create([
                'due_date' => now()->day(15)->format("Y-m-d"),
                "installments" => 10,
                'amount' => 500,
            ]);
        foreach ($debts as $debt) {
            $movement = $debt->movements->first();
            $movement->user_id = $debt->user_id;
            $movement->identifier_id = $debt->identifier_id;
            $movement->save();
        }

        $this->artisan('app:cmtd');

        $this->assertDatabaseCount('movements', 2);
        $this->assertCount(
            2,
            Movement::query()->where([
                'movementable_type' => Debt::class,
                'type' => 'out',
                'effetive_date' => now()->subDay()->format('Y-m-d'),
                'amount' => 50,
            ])
                ->whereIn('user_id', [$debts->get(0)->user_id, $debts->get(1)->user_id])
                ->get()
        );
    }

    public function test_handle_method_with_available_debt_but_trashed_movement_to_this_month(): void
    {
        $this->travelTo(now()->day(10));

        $debts = Debt::factory(2)
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
                'due_date' => now()->day(15)->format("Y-m-d"),
                'amount' => 500,
            ]);
        foreach ($debts as $debt) {
            $movement = $debt->movements()->withTrashed()->first();
            $movement->user_id = $debt->user_id;
            $movement->identifier_id = $debt->identifier_id;
            $movement->save();
        }

        $this->artisan('app:cmtd');

        $this->assertDatabaseCount('movements', 2);
        $this->assertCount(
            2,
            Movement::withTrashed()->where([
                'movementable_type' => Debt::class,
                'type' => 'out',
                'effetive_date' => now()->subDay()->format('Y-m-d'),
                'amount' => 500,
            ])
                ->whereIn('user_id', [$debts->get(0)->user_id, $debts->get(1)->user_id])
                ->get()
        );
    }
}
