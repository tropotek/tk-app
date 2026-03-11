<?php
namespace Tk\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Tk\Menu\MenuInterface build(string $name)
 */
class Menu extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \Tk\Menu\MenuBuilder::class;
    }
}
