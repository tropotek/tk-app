<?php
/**
 *
 */
namespace Tk\Menu;

use Illuminate\Support\Facades\File;

/**
 * This is the menu builder class to the alias function `menu('name')`.
 *
 * Add new menus in the `/app/Actions/Builder/Menu` directory.
 *
 * Blade Example:
 * ```
 *  @foreach (menu('navbar') as $item)
 *      <x-nav-item :item="$item" level="0" class="nav-item" submenu-class="dropdown-menu" link-class="nav-link" />
 *  @endforeach
 * ```
 *
 */
class MenuBuilder
{
    public static function make(): self
    {
        return new self;
    }


    /**
     * Search the menus directory for available menu builder objects
     * The $builder name should match the base class name of the menu builder object.
     * E.G: 'StaffNav' will resolve to `App\Menus\StaffNav`
     */
    public function build(string $builder =  ''): MenuInterface
    {
        $builders = collect();

        foreach (config('tk-base.menu_builders') as $namespace => $path) {
            foreach (File::files($path) as $file) {
                $class = $namespace . $file->getFilenameWithoutExtension();
                if (class_exists($class)) {
                    $builders->put(strtolower($file->getFilenameWithoutExtension()), $class);
                }
            }
        }

        $menuBuilder = $builders->get(strtolower($builder));
        if (empty($menuBuilder)) {
            throw new \Exception("Menu builder $builder not found.");
        }

        $menu = (new $menuBuilder)->build();
        return $menu->removeHiddenItems();
    }
}
