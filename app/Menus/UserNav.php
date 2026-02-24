<?php

namespace App\Menus;

use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

final class UserNav extends MenuInterface
{

    public function build(): static
    {
        $this->menu = collect([
            MenuItem::make('Dashboard', '/')->setIcon('fa-solid fa-gauge'),
            MenuItem::make('My Profile', '/')->setIcon('fa-solid fa-user'),
            MenuItem::makeSeparator(),
            MenuItem::make('About', '#')->setIcon('fa-solid fa-circle-info'),
            MenuItem::make('Logout', '/logout')->setIcon('fa-solid fa-right-from-bracket'),
        ]);

        // remove non visible items
        $this->menu = $this->menu->reject(fn(MenuItem $menu) => !$menu->isVisible());

        return $this;
    }
}
