<?php

namespace App\Providers;

use App\Menus\NavBar;
use App\Menus\UserNav;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Tk\Support\Facades\MenuBuilder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Config paginator
        Paginator::useBootstrapFive();

        Model::unguard();
        Model::shouldBeStrict();
        Model::automaticallyEagerLoadRelationships();

        MenuBuilder::registerBuilder(NavBar::class, 'NavBar');
        MenuBuilder::registerBuilder(UserNav::class, 'UserNav');

        Gate::define('accessAdmin', fn (User $user) => $user->isStaff());

        $this->applySiteSettings();
    }

    /**
     * Push the DB-backed site settings into the standard config keys, so the
     * rest of the app (page titles, mail from address, registration gate)
     * keeps reading from config() as normal.
     */
    private function applySiteSettings(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $settings = Setting::current();

        if ($settings->site_title) {
            config(['app.name' => $settings->site_title]);
        }

        config(['app.registration_enabled' => $settings->enable_user_reg]);

        if ($settings->site_email) {
            config([
                'mail.from.address' => $settings->site_email,
                'mail.from.name' => config('app.name'),
            ]);
        }
    }
}
