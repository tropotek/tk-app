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
    const int CACHE_HOURS = 4;

    /**
     * Search the menus directory for available menu builder objects
     * The menu will be cached for production/staging environments.
     * The $name 'StaffNav' will resolve to `\App\Menu\Menus\StaffNav`
     */
    public function build(string $name =  ''): MenuInterface
    {
//        $sid = 'menu_'.$name;

        // get cached menu
//        $menu = Session::cache()->get($sid);
//        if (
//            $menu instanceof MenuInterface &&
//            app()->environment(['production', 'staging'])
//        ) {
//            return $menu;
//        }

        $menuClass = '';
        foreach (config('tk-base.menu_builders') as $namespace) {
            $menuClass = $namespace . $name;
            if (class_exists($menuClass)) {
                break;
            }
        }
        if (!class_exists($menuClass)) {
            throw new \Exception("Menu builder object for $name not found.");
        }

        /** @var MenuInterface $menu */
        $menu = (new $menuClass('_top'))->build();
        $menu->removeHidden();

        // cache menu
//        Session::cache()->put($sid, $menu, now()->addHours(self::CACHE_HOURS));

        return $menu;
    }
}
