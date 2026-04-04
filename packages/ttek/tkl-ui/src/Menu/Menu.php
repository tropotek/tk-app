<?php

namespace Tk\Menu;

final class Menu extends MenuItem
{

    public function __construct(string $label)
    {
        parent::__construct($label);
    }

    // override menu item methods for a parent menu

    public function getUrl(): string
    {
        return '';
    }

    public function isTitleVisible(): bool
    {
        return false;
    }

    public function isVisible(): bool
    {
        return false;
    }

    public function showUrl(): bool
    {
        return false;
    }
}
