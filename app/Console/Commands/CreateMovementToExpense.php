<?php

namespace App\Console\Commands;

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
            ->whereHas("expenses", function (Builder $query) {
                // $query->whereDoesntHave("movements", function (Builder $query) {
                //     $query->whereYear("effetive_date", now()->year)
                //         ->whereMonth("effetive_date", now()->month);
                // });
            })
            ->get();

        dd($users);
    }
}
