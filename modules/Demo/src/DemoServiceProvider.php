<?php

namespace Demo;

use App\Models\User;
use Demo\Menus\NavBar;
use Demo\Models\Idea;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Tk\Support\Facades\MenuBuilder;

class DemoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadViewsFrom(__DIR__.'/../resources/views/pages', 'demo');

        if (class_exists(Livewire::class)) {
            Livewire::addNamespace(namespace: 'demo', viewPath: __DIR__.'/../resources/views/pages');
        }

        MenuBuilder::registerBuilder(NavBar::class, 'NavBar');

        User::resolveRelationUsing('ideas', fn (User $user) => $user->hasMany(Idea::class));
    }
}
