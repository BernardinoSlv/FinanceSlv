<?php

namespace App\Providers;

use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\UserRepositoryContract;
use App\Repositories\EntryRepository;
use App\Repositories\LeaveRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(
            UserRepositoryContract::class,
            UserRepository::class
        );

        $this->app->bind(
            EntryRepositoryContract::class,
            EntryRepository::class
        );

        $this->app->bind(
            LeaveRepositoryContract::class,
            LeaveRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
