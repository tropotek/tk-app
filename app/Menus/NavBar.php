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
            MenuItem::make('Home', route('home'))
                ->setVisible(!auth()->check()),
            MenuItem::make('Dashboard', route('dashboard'))
                ->setVisible(auth()->check()),

            MenuItem::make('Examples')->addChildren([
                MenuItem::make('One Column', '/formOne'),
                MenuItem::make('Two Columns', '/formTwo'),
                MenuItem::make('Three Columns', '/formThree'),
                MenuItem::make('Fieldsets', '/formFieldset'),
                MenuItem::makeSeparator(),
                MenuItem::make('Livewire', '/tableLivewire'),
                MenuItem::make('Sql Query', '/tableQuery'),
                MenuItem::make('Array Rows', '/tableArray'),
                MenuItem::make('Csv File', '/tableCsv'),
                MenuItem::makeSeparator(),
                MenuItem::make('Ideas Example', '/ideas')
                    ->setVisible(auth()->check()),
                MenuItem::makeSeparator(),
                MenuItem::make('Layout Example', '/examples'),
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
        $this->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

        return $this;
    }
}
