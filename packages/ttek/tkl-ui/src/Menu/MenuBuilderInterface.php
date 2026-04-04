<?php

namespace Tk\Menu;

/**
 * Use this for your module Menu builders.
 * Register your Menu builder in your ServiceProvider class:
 * ```
 * public function boot(): void
 * {
 *      \Tk\Support\Facades\Menu::registerBuilder(\App\Menus\NavBar::class);
 *      // use a namespace for multiple menus
 *      \Tk\Support\Facades\Menu::registerBuilder(\App\Menus\UserNav::class, 'UserNav');
 * }
 * ```
 *
 */
interface MenuBuilderInterface
{
    /**
     * Add your menu items here
     */
    public function build(Menu $menu): void;
}
