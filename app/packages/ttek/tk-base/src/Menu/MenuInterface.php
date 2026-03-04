<?php

namespace Tk\Menu;

abstract class MenuInterface extends MenuItem
{
    abstract public function build(): static;


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
