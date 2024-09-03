<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Debt;
use App\Models\Identifier;
use App\Models\Movement;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
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
        Gate::define('is-owner', function (User $user, Model $record): bool {
            return $user->id === $record->user_id;
        });
    }
}
