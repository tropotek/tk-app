<?php

namespace App\Menus;

use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

final class UserNav extends MenuInterface
{

    public function build(): static
    {
        $this->addChildren([
            MenuItem::make('Dashboard', '/')->setIcon('fa-solid fa-gauge'),
            MenuItem::make('My Profile', '/')->setIcon('fa-solid fa-user'),
            MenuItem::makeSeparator(),
            MenuItem::make('About')
                ->addAttribute(['data-bs-toggle' => 'modal', 'data-bs-target' => '#aboutModal'])
                ->setIcon('fa-solid fa-circle-info'),
            MenuItem::make('Logout', '/logout')->setIcon('fa-solid fa-right-from-bracket'),
        ]);

        // Reset breadcrumbs if menu item selected
        $this->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);
        return $this;
    }
}
