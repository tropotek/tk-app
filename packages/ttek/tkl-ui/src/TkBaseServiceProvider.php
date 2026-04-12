<?php

namespace Tk;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Support\Facades\MenuBuilder;
use Tk\View\Composers\DefaultPageName;
use Illuminate\Pagination\Paginator;

class TkBaseServiceProvider extends ServiceProvider
{

    public function register()
    {
        // Register bindings in the IoC container
        $this->mergeConfigFrom(
            __DIR__.'/../config/tkbase.php', 'tkl-ui'
        );

        // MenuBuilder
        $this->app->singleton('menu-builder', function () {
            return new \Tk\Menu\MenuBuilder();
        });
        AliasLoader::getInstance()->alias('MenuBuilder', MenuBuilder::class);

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

        // Livewire Volt component namespace
        if (class_exists(\Livewire\Livewire::class)) {
            Livewire::addNamespace(
                namespace: 'tkl-pages',
                viewPath: __DIR__.'/../Resources/views/pages',
                classNamespace: 'Modules\\Staff\\Livewire',
                classPath: __DIR__.'/../Livewire',
                classViewPath: __DIR__.'/../Resources/views/pages',
            );
            Livewire::addNamespace('tkl-com', __DIR__.'/../resources/views/components');
        }

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tkl-ui');

        // set a default controller TITLE if none set
        View::composer('*', DefaultPageName::class);


        // Load routes (optional)
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
