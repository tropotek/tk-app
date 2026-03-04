<?php

namespace Tk;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Support\Facades\Menu;

class TkBaseServiceProvider extends ServiceProvider
{

    public function register()
    {
        // Register bindings in the IoC container
        $this->mergeConfigFrom(
            __DIR__.'/../config/tkbase.php', 'tk-base'
        );

        // Menu
        AliasLoader::getInstance()->alias('Menu', Menu::class);

        // Breadcrumbs
        AliasLoader::getInstance()->alias('Breadcrumbs', Breadcrumbs::class);
        // TODO: need to consider if the app should handle this part ????
        $this->app->bind(\Tk\Breadcrumbs\Breadcrumbs::class, function() {
            // Get the breadcrumbs from the session if they exist
            $breadcrumbs = Session::get(\Tk\Breadcrumbs\Breadcrumbs::class);
            if ($breadcrumbs instanceof \Tk\Breadcrumbs\Breadcrumbs) {
                return $breadcrumbs;
            }

            // Create a new Breadcrumb instance
            $breadcrumbs = new \Tk\Breadcrumbs\Breadcrumbs();
            Session::put(\Tk\Breadcrumbs\Breadcrumbs::class, $breadcrumbs);
            return $breadcrumbs;
        });

    }

    public function boot()
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../config/tkbase.php' => config_path('tkbase.php'),
        ], 'config');

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tk-base');

        // Load routes (optional)
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load migrations (optional)
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
