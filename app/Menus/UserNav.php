<?php

namespace App\Menus;

use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\MenuBuilderInterface;
use Tk\Menu\Menu;
use Tk\Menu\MenuItem;

final class UserNav implements MenuBuilderInterface
{
    public function build(Menu $menu): void
    {
        $menu->addChildren([
            MenuItem::make('Dashboard', '/')->setIcon('fa-solid fa-gauge'),
            MenuItem::make('My Profile', '/')->setIcon('fa-solid fa-user'),
            MenuItem::makeSeparator(),
            MenuItem::make('About')
                ->addAttribute(['data-bs-toggle' => 'modal', 'data-bs-target' => '#aboutModal'])
                ->setIcon('fa-solid fa-circle-info'),
            MenuItem::make('Logout', '/logout')->setIcon('fa-solid fa-right-from-bracket'),
        ]);

        // Reset breadcrumbs if menu item selected
        $menu->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

    }
}
