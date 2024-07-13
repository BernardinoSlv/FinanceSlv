<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive("amount", function ($amount): string {
            return "<?php echo 'R$ ' . number_format(floatval($amount), 2, ',', '.'); ?>";
        });

        Model::preventLazyLoading(!app()->isProduction());
    }
}
