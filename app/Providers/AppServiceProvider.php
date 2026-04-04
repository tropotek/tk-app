<?php

namespace App\Providers;

use App\Menus\NavBar;
use App\Menus\UserNav;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Tk\Support\Facades\MenuBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('admin-view', function(?User $user) {
            if ($user->isAdmin()) {
                return Response::allow();
            }
            return Response::denyAsNotFound();
        });

        // Config paginator
        Paginator::useBootstrapFive();

        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();

        MenuBuilder::registerBuilder(NavBar::class, 'NavBar');
        MenuBuilder::registerBuilder(UserNav::class, 'UserNav');

    }

}
