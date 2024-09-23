<?php

namespace App\Console\Commands;

use App\Enums\MovementTypeEnum;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreateMovementToExpense extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cmte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create automatic movements to expense';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::query()
            ->with('expenses', function (HasMany $query) {
                $query->whereDoesntHave('movements', function (Builder $query) {
                    $query->withTrashed()->whereYear('effetive_date', now()->year)
                        ->whereMonth('effetive_date', now()->month);
                })
                    ->where('due_day', '>=', now()->day);
            })
            ->whereHas('expenses', function (Builder $query) {
                $query->whereDoesntHave('movements', function (Builder $query) {
                    $query->withTrashed()->whereYear('effetive_date', now()->year)
                        ->whereMonth('effetive_date', now()->month);
                })
                    ->where('due_day', '>=', now()->day);
            })
            ->get();

        foreach ($users as $user) {
            foreach ($user->expenses as $expense) {
                $expense->movements()->create([
                    'user_id' => $user->id,
                    'identifier_id' => $expense->identifier_id,
                    'type' => MovementTypeEnum::OUT->value,
                    'effetive_date' => now()->day($expense->due_day),
                    'closed_date' => null,
                    'amount' => $expense->amount,
                    'fees_amount' => 0,
                ]);
            }
        }
        // dd($users);
    }
}
