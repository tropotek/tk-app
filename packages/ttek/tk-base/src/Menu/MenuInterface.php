<?php

namespace Tk\Menu;

use Illuminate\Support\Collection;

abstract class MenuInterface
{
    protected Collection $menu;

    /**
     * @return array<int,MenuItem>
     */
    public function getItems(): array
    {
        return $this->menu->all();
    }

    /**
     * Iterate items and remove any that are hidden
     */
    public function removeHiddenItems(): static
    {
        $this->menu = $this->menu->reject(fn(MenuItem $itm) => !$itm->isVisible());
        return $this;
    }

    abstract public function build(): static;
}
