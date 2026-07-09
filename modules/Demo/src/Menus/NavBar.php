<?php

namespace Demo\Menus;

use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Menu\Menu;
use Tk\Menu\MenuBuilderInterface;
use Tk\Menu\MenuItem;

final class NavBar implements MenuBuilderInterface
{
    public function build(Menu $menu): void
    {
        $examples = MenuItem::make('Examples')->addChildren([
            MenuItem::make('Form Fieldgroup', route('examples.formThree')),
            MenuItem::make('Form Fieldset', route('examples.formFieldset')),
            MenuItem::makeSeparator(),
            MenuItem::make('Table Test', route('examples.tableTest')),
            MenuItem::make('Table Arrays', route('examples.tableArray')),
            MenuItem::make('Table Arrays (Livewire)', route('examples.tableArray2')),
            MenuItem::makeSeparator(),
            MenuItem::make('Ideas CRUD Example', route('examples.ideas.index'))
                ->setVisible(auth()->check()),
            MenuItem::makeSeparator(),
            MenuItem::make('Page Layout Example', route('examples.index')),
            MenuItem::make('Bootstrap Elements', route('examples.bootstrap')),
        ]);

        // Reset breadcrumbs if a menu item is selected — applied here rather than
        // on the shared $menu, since the host's own NavBar builder already ran its
        // appendQuery() call before this builder executes and won't touch our items.
        $examples->appendQuery([Breadcrumbs::CRUMB_RESET => '1']);

        $menu->addChild($examples);
    }
}
