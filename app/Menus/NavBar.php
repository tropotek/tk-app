<?php

namespace App\Menus;

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
                MenuItem::make('Livewire', '/examples/tableLivewire'),
                MenuItem::make('Livewire 2', '/examples/tableLivewireTwo'),
                MenuItem::make('Sql Query', '/examples/tableQuery'),
                MenuItem::make('Array Rows', '/examples/tableArray'),
                MenuItem::make('Csv File', '/examples/tableCsv'),
                MenuItem::makeSeparator(),
                MenuItem::make('Ideas Example', '/examples/ideas')
                    ->setVisible(auth()->check()),
                MenuItem::makeSeparator(),
                MenuItem::make('Layout Example', '/examples/examples'),
            ]),

            MenuItem::make('Admin')->addChildren([
                MenuItem::make('Settings', '/')->setDisabled(),
                MenuItem::make('Users', '/users'),
                MenuItem::make('Staff', '/staff'),
                MenuItem::make('Member', '/members')->setDisabled(),
            ])->setVisible(auth()->check()),

//            MenuItem::make('Test')->addChildren([
//                MenuItem::make('Sql Query', '/tableQuery'),
//                MenuItem::make('Array Rows', '/tableArray'),
//                MenuItem::make('Csv File', '/tableCsv'),
//            ])->setVisible(auth()->check()),

            MenuItem::make('Logout', '/logout')
                ->setTitleVisible(false)
                ->setIcon('fa-solid fa-right-from-bracket')
                ->setVisible(auth()->check()),

        ]);

        // Reset breadcrumbs if menu item selected
        $menu->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

    }
}
