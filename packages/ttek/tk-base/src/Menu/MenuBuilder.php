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

    public function build(string $builder =  ''): MenuInterface
    {
        $builders = collect();

        // generate a map of all available menus.
        // TODO: Cache the $builders list, (find out how to do the with Laravel)
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

        return (new $menuBuilder)->build();
    }
}
