<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Identifier;
use App\Models\Entry;
use App\Models\Expense;
use App\Models\Investiment;
use App\Models\Leave;
use App\Models\Need;
use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define("entry-edit", function (User $user, Entry $entry): bool {
            return $user->id === $entry->user_id;
        });

        Gate::define("leave-edit", function (User $user, Leave $leave): bool {
            return $user->id === $leave->user_id;
        });

        Gate::define("expense-edit", function (User $user, Expense $expense): bool {
            return $user->id === $expense->user_id;
        });

        Gate::define("debtor-edit", function (User $user, Debtor $debtor): bool {
            return $user->id ===  $debtor->user_id;
        });

        Gate::define("debt-edit", function (User $user, Debt $debt): bool {
            return $user->id === $debt->user_id;
        });

        Gate::define("investiment-edit", function (User $user, Investiment $investiment): bool {
            return $user->id === $investiment->user_id;
        });

        Gate::define("need-edit", function (User $user, Need $need): bool {
            return $user->id === $need->user_id;
        });

        Gate::define("identifier-edit", function (User $user, Identifier $identifier): bool {
            return $user->id === $identifier->user_id;
        });
    }
}
