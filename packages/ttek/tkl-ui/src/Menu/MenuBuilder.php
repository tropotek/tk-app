<?php
/**
 *
 */
namespace Tk\Menu;

use Illuminate\Support\Facades\Session;

/**
 * Get a MenuInterface object based on its class basename.
 * Add new menus in the `/app/Actions/Builder/Menu` directory.
 *
 * Alias: `\Menu` => `\Tk\Menu\Facades\Menu`
 * helper: `\Tk\MenuInterface menu(string)`
 *
 *
 * Blade Example:
 * ```
 *  @foreach (menu('navbar')->getItems() as $item)
 *      <x-nav-item :item="$item" level="0" class="nav-item" submenu-class="dropdown-menu" link-class="nav-link" />
 *  @endforeach
 * ```
 *
 */
class MenuBuilder
{
    protected array $builders = [];


    /**
     * Register a menu builder class
     */
    public function registerBuilder(string $builderClass, string $namespace = 'menu'): void
    {
        $this->builders[$namespace][] = $builderClass;
    }

    /**
     * Compile all registered builders for a menu and return that menu
     */
    public function compileMenu(string $namespace = 'menu'): Menu
    {

        if (!isset($this->builders[$namespace])) {
            throw new \Exception("no menu builders found for namespace $namespace");
        }

        $menu = Session::cache()->get($this->getSid($namespace));
        //if ($menu instanceof Menu) {
        if ($menu instanceof Menu && app()->environment(['production', 'staging'])) {
            return $menu;
        }

        $menu = new Menu($namespace);

        foreach (($this->builders[$namespace] ?? []) as $builderClass) {
            if (class_exists($builderClass)) {
                $builder = app($builderClass);
                if ($builder instanceof \Tk\Menu\MenuBuilderInterface) {
                    $builder->build($menu);
                }
            }
        }

        $menu->normalize();

        Session::cache()->put($this->getSid($namespace), $menu);

        return $menu;
    }

    public function clearCache(string $namespace = 'menu'): void
    {
        Session::cache()->forget($this->getSid($namespace));
    }

    /**
     * return the session id for a menu
     */
    private function getSid($namespace): string
    {
        return 'menu_' . $namespace;
    }

}
