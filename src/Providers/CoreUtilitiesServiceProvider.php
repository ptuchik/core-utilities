<?php

namespace Ptuchik\CoreUtilities\Providers;

use Illuminate\Support\ServiceProvider;
use Ptuchik\CoreUtilities\Helpers\DataStorage;

use function request;

/**
 * Class CoreUtilitiesServiceProvider
 *
 * @package Ptuchik\CoreUtilities\Providers
 */
class CoreUtilitiesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish a config file
        $this->publishes([
            __DIR__.'/../../config/core-utilities.php' => config_path('ptuchik-core-utilities.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../../config/core-utilities.php', 'ptuchik-core-utilities');

        $this->app->singleton(DataStorage::class, function () {
            return new DataStorage(request()->all());
        });
    }
}
