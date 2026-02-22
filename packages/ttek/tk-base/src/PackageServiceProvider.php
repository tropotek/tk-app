<?php

namespace Tk;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{

    public function register()
    {
        // Register bindings in the IoC container
        $this->mergeConfigFrom(
            __DIR__.'/config/tkbase.php', 'tk-base'
        );
    }

    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/config/tkbase.php' => config_path('tkbase.php'),
        ], 'config');

        // Load views
        $this->loadViewsFrom(__DIR__.'/resources/views', 'tk-base');

        // Load routes (optional)
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load migrations (optional)
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
