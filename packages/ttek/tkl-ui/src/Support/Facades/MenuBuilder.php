<?php
namespace Tk\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void registerBuilder(string $builderClass, string $namespace = 'menu')
 * @method static \Tk\Menu\Menu compileMenu(string $namespace = 'menu')
 */
class MenuBuilder extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \Tk\Menu\MenuBuilder::class;
    }
}
