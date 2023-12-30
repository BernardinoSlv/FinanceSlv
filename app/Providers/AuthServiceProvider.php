<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Entry;
use App\Models\Leave;
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
    }
}
