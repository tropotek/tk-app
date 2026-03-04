<?php

namespace Tk;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Support\Facades\Menu;
use Tk\View\Composers\DefaultPageTitle;

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
        // bind the Breadcrumbs Alias to an existing Breadcrumbs instance if exists
        $this->app->bind(\Tk\Breadcrumbs\Breadcrumbs::class, function() {
            if (auth()->check()) {
                return \Tk\Breadcrumbs\Breadcrumbs::make('Dashboard', route('dashboard'));
            }
            return \Tk\Breadcrumbs\Breadcrumbs::make('Home', route('home'));
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

        // set a default controller TITLE if none set
        View::composer('*', DefaultPageTitle::class);

        // Load routes (optional)
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load migrations (optional)
        // $this->loadMigrationsFrom(__DIR__.'/migrations');
    }
}
