<?php

namespace Tk\Menu;

use Illuminate\Support\Collection;

abstract class MenuInterface
{
    protected Collection $menus;

    /**
     * @return array<int,MenuItem>
     */
    public function menus(): array
    {
        return $this->menus->all();
    }

    abstract public function build(): static;
}
