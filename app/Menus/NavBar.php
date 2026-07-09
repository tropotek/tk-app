<?php

namespace App\Menus;

use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\Menu;
use Tk\Menu\MenuBuilderInterface;
use Tk\Menu\MenuItem;

final class NavBar implements MenuBuilderInterface
{
    public function build(Menu $menu): void
    {
        $menu->addChildren([
            MenuItem::make('Home', route('home'))
                ->setVisible(! auth()->check()),
            MenuItem::make('Dashboard', route('dashboard'))
                ->setVisible(auth()->check()),

            MenuItem::make('Examples')->addChildren([
                MenuItem::make('Form Fieldgroup', route('examples.formThree')),
                MenuItem::make('Form Fieldset', route('examples.formFieldset')),
                MenuItem::makeSeparator(),
                MenuItem::make('Table Test', route('examples.tableTest')),
                MenuItem::make('Table Arrays', route('examples.tableArray')),
                MenuItem::make('Table Arrays (Livewire)', route('examples.tableArray2')),
                MenuItem::makeSeparator(),
                MenuItem::make('Ideas CRUD Example', route('examples.ideas.index'))
                    ->setVisible(auth()->check()),
                MenuItem::makeSeparator(),
                MenuItem::make('Page Layout Example', route('examples.index')),
                MenuItem::make('Bootstrap Elements', route('examples.bootstrap')),
            ]),

            MenuItem::make('Admin')->addChildren([
                MenuItem::make('Settings', route('admin.settings'))
                    ->setVisible(auth()->check() && auth()->user()->isAdmin()),
                MenuItem::make('Users', route('admin.users.index')),
                MenuItem::makeSeparator(),
                MenuItem::make('Phpinfo', route('admin.phpinfo')),
                MenuItem::make('User', route('admin.dump-user')),
                MenuItem::make('Session', route('admin.dump-session')),
            ])->setVisible(auth()->check() && auth()->user()->isStaff()),

            MenuItem::make('Logout', route('logout'))
                ->setTitleVisible(false)
                ->setIcon('fa-solid fa-right-from-bracket')
                ->setVisible(auth()->check()),

        ]);

        // Reset breadcrumbs if menu item selected
        $menu->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

    }
}
