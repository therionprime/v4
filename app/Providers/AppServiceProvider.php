<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('admin', function () {
            return '<?php if (Auth::check() && Auth::user()->isAdmin): ?>';
        });
        Blade::directive('notadmin', function () {
            return '<?php  else: ?>';
        });
        Blade::directive('endadmin', function () {
            return '<?php endif; ?>';
        });

        Blueprint::macro('analytics', function () {
            $this->bigInteger('impressions')->nullable();
            $this->bigInteger('downloads')->nullable();
            $this->bigInteger('installs')->nullable();
            $this->bigInteger('uses')->nullable();
        });

        Blueprint::macro('dropAnalytics', function () {
            $this->dropColumn('impressions');
            $this->dropColumn('downloads');
            $this->dropColumn('installs');
            $this->dropColumn('uses');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
