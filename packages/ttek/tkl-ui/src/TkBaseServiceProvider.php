<?php

namespace Tk;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
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

        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tkl-ui');

        // set a default controller TITLE if none set
        View::composer('*', DefaultPageName::class);

        // Register Livewire components
        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::addComponent('tkl-file-upload', viewPath: __DIR__ . '/../resources/views/livewire/⚡file-upload.blade.php');
        }

        // Load routes (optional)
        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');

        // Load migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
