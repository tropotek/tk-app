<?php

namespace App\Menus;

use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

final class NavBar extends MenuInterface
{

    public function build(): static
    {
        $this->addChildren([
            MenuItem::make('Dashboard', '/'),

            MenuItem::make('Forms')->addChildren([
                MenuItem::make('One Column', '/formOne'),
                MenuItem::make('Two Columns', '/formTwo'),
                MenuItem::make('Three Columns', '/formThree'),
                MenuItem::make('Fieldsets', '/formFieldset'),
            ]),

            MenuItem::make('Tables')->addChildren([
                MenuItem::make('Sql Query', '/tableQuery'),
                MenuItem::make('Array Rows', '/tableArray'),
                MenuItem::make('Csv File', '/tableCsv'),
            ]),

            MenuItem::make('Layout', '/examples'),

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
                    ->setVisible(auth()->hasUser()),    // eg: set a required permission
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

        // Reset breadcrumbs if menu item selected
        $this->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

        return $this;
    }
}
