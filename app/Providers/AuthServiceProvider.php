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
use App\Models\Movement;
use App\Models\Need;
use App\Models\Quick;
use App\Models\QuickEntry;
use App\Models\QuickLeave;
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
        Gate::define("quick-edit", function (User $user, Quick $quick): bool {
            return $user->id === $quick->user_id;
        });

        Gate::define("identifier-edit", function (User $user, Identifier $identifier): bool {
            return $user->id === $identifier->user_id;
        });

        Gate::define("movement-edit", function (User $user, Movement $movement): bool {
            return $user->id === $movement->user_id;
        });
    }
}
