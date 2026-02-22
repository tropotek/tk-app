<?php

namespace App\Menus;

use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

final class NavBar extends MenuInterface
{

    public function build(): static
    {
        $this->menus = collect([
            MenuItem::make('Dashboard', '/'),
            MenuItem::make('Form Examples')->addChildren([
                MenuItem::make('One Column', '/formOne'),
                MenuItem::make('Two Columns', '/formTwo'),
                MenuItem::make('Three Columns', '/formThree'),
                MenuItem::make('Fieldsets', '/formFieldset'),
            ]),
            MenuItem::make('Examples', '/examples'),

            MenuItem::make('Ideas', '/ideas')
                ->setVisible(auth()->check()
            ),

            MenuItem::make('Dropdown')->addChildren([
                MenuItem::make('Manage PCE', '/'),
                MenuItem::make('Manage FCE', '/')->setDisabled(true),
                MenuItem::makeSeparator(),
                MenuItem::make('Registrar Dashboard', '/')->setDisabled(true),
                MenuItem::make('Manage Open Grades', '/')->setDisabled(true)
                    ->setDisabled(true)
                    ->setVisible(fn() => auth()->hasUser()),    // eg: set a required permission
            ]),

            MenuItem::make('Admin')->addChildren([
                MenuItem::make('Ideas', '/ideas'),
                MenuItem::makeSeparator(),
                MenuItem::make('Countries', '/'),
                MenuItem::make('Regions', '/'),
                MenuItem::make('Currencies', '/'),
                MenuItem::make('Programs', '/'),
            ])->setVisible(auth()->check() && auth()->user()->can('admin-view')),

            MenuItem::make('Logout', '/logout')
                ->setTitleVisible(false)
                ->setIcon('fa-solid fa-right-from-bracket'),

        ]);

        // remove non visible items
        $this->menus = $this->menus->reject(fn(MenuItem $menu) => !$menu->isVisible());

        return $this;
    }
}
