<?php

namespace App\Console\Commands;

use App\Enums\MovementTypeEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreateMovementToDebt extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cmtd';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create movements to debt ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::query()
            ->with('debts', function (HasMany $query) {
                $query->whereDoesntHave('movements', function (Builder $query) {
                    $query->withTrashed()->whereYear('effetive_date', now()->year)
                        ->whereMonth('effetive_date', now()->month);
                })
                    ->where('due_date', '>=', now())
                    ->withSum("movements", "amount");
            })
            ->whereHas('debts', function (Builder $query) {
                $query->whereDoesntHave('movements', function (Builder $query) {
                    $query->withTrashed()->whereYear('effetive_date', now()->year)
                        ->whereMonth('effetive_date', now()->month);
                })
                    ->where('due_date', '>=', now());
            })
            ->get();

        foreach ($users as $user) {
            foreach ($user->debts as $debt) {
                $debt->movements()->create([
                    'user_id' => $user->id,
                    'identifier_id' => $debt->identifier_id,
                    'type' => MovementTypeEnum::OUT->value,
                    'effetive_date' => now()->day($debt->due_date->day),
                    'closed_date' => null,
                    'amount' => ($debt->amount + floatval($debt->movements_sum_amount)) / $debt->installments,
                    'fees_amount' => 0,
                ]);
            }
        }
    }
}
