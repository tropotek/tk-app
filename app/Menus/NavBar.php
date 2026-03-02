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

            MenuItem::make('Ideas Example', '/ideas')
                ->setVisible(auth()->check()),

            MenuItem::make('Layout Example', '/examples'),

            MenuItem::make('Logout', '/logout')
                ->setTitleVisible(false)
                ->setIcon('fa-solid fa-right-from-bracket')
                ->setVisible(auth()->check()),

        ]);

        // Reset breadcrumbs if menu item selected
        $this->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

        return $this;
    }
}
