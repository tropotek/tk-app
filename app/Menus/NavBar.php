<?php

namespace App\Menus;

use App\Enum\Roles;
use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\MenuBuilderInterface;
use Tk\Menu\Menu;
use Tk\Menu\MenuItem;

final class NavBar implements MenuBuilderInterface
{

    public function build(Menu $menu): void
    {
        $menu->addChildren([
            MenuItem::make('Home', route('home'))
                ->setVisible(!auth()->check()),
            MenuItem::make('Dashboard', route('dashboard'))
                ->setVisible(auth()->check()),

            MenuItem::make('Examples')->addChildren([
                MenuItem::make('One Column', '/examples/formOne'),
                MenuItem::make('Two Columns', '/examples/formTwo'),
                MenuItem::make('Three Columns', '/examples/formThree'),
                MenuItem::make('Fieldsets', '/examples/formFieldset'),
                MenuItem::makeSeparator(),
                MenuItem::make('Array Rows (sis)', '/examples/tableArray'),
                MenuItem::make('Array Rows (sis live)', '/examples/tableArray2'),
                MenuItem::makeSeparator(),
                MenuItem::make('Ideas Example', route('examples.ideas.index'))
                    ->setVisible(auth()->check()),
                MenuItem::makeSeparator(),
                MenuItem::make('Layout Example', '/examples/examples'),
            ]),

            MenuItem::make('Admin')->addChildren([
                MenuItem::make('Settings', route('dashboard'))->setDisabled(),
                MenuItem::make('Users', route('admin.users.index')),
            ])->setVisible(auth()->user()->hasRole(Roles::Admin->value)),

            MenuItem::make('Logout', route('logout'))
                ->setTitleVisible(false)
                ->setIcon('fa-solid fa-right-from-bracket')
                ->setVisible(auth()->check()),

        ]);

        // Reset breadcrumbs if menu item selected
        $menu->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

    }
}
