# Menu Builder

## What it does

The menu system provides a structured way to build navigation menus in PHP and render them in Blade.

It consists of:

- a **facade**: `\Tk\Support\Facades\Menu`
- a **builder service**: `\Tk\Menu\MenuBuilder`
- a **base menu class**: `\Tk\Menu\MenuInterface`
- **menu items**: `\Tk\Menu\MenuItem`
- a reusable **Blade renderer**: `x-tk-base::navitem`
- a helper function: `menu('YourMenuClass')`

In short: you create a menu class in `App\Menus`, return menu items from `build()`, and render the result in Blade.

---

## Related files

Main files involved:

- `app/packages/ttek/tk-base/src/Support/Facades/Menu.php`
- `app/packages/ttek/tk-base/src/Menu/MenuBuilder.php`
- `app/packages/ttek/tk-base/src/Menu/MenuInterface.php`
- `app/packages/ttek/tk-base/src/Menu/MenuItem.php`
- `app/packages/ttek/tk-base/src/helpers.php`
- `app/packages/ttek/tk-base/resources/views/components/navitem.blade.php`
- `app/packages/ttek/tk-base/config/tkbase.php`
- `app/packages/ttek/tk-base/src/TkBaseServiceProvider.php`

---

## How it is accessed

You can use either the facade or the helper.

### Facade

```php
use Tk\Support\Facades\Menu;

$menu = Menu::build('MainNav');
```


### Helper

```php
$menu = menu('MainNav');
```


The helper is just a convenience wrapper around the facade.

---

## How menu resolution works

`MenuBuilder::build($name)` looks through the namespaces listed in:

```php
config('tk-base.menu_builders')
```


Current default configuration:

```php
[
    'App\\Menus\\',
]
```


So:

```php
menu('MainNav')
```


will try to resolve:

```php
App\Menus\MainNav
```


If the class does not exist, an exception is thrown.

---

## Creating a menu class

A menu class should extend `\Tk\Menu\MenuInterface` and implement:

```php
public function build(): static
```


Because `MenuInterface` extends `MenuItem`, the menu itself acts as the root container and can hold child items.

### Example menu class

```php
<?php

namespace App\Menus;

use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

class MainNav extends MenuInterface
{
    public function build(): static
    {
        $this->addChildren([
            MenuItem::make('Dashboard', route('dashboard')),
            MenuItem::make('Ideas', route('ideas.index')),
            MenuItem::make('Users', route('users.index')),
        ]);

        return $this;
    }
}
```


Then render it with:

```blade
@foreach (menu('MainNav')->getChildren() as $item)
    <x-tk-base::navitem
        :item="$item"
        level="0"
        class="nav-item"
        submenu-class="dropdown-menu"
        link-class="nav-link"
    />
@endforeach
```


---

## Why `getChildren()` is the correct way to iterate

The root menu object extends `MenuItem`, but `MenuInterface` intentionally hides normal item behavior:

- `getUrl()` always returns an empty string
- `isTitleVisible()` returns `false`
- `isVisible()` returns `false`
- `showUrl()` returns `false`

That means the root menu is just a container.

To render top-level items, iterate over:

```php
$menu->getChildren()
```


not the menu object itself as a link.

---

## Creating menu items

Use `MenuItem::make()`:

```php
use Tk\Menu\MenuItem;

$item = MenuItem::make('Dashboard', route('dashboard'));
```


### Available common methods

```php
$item->setLabel('Dashboard');
$item->setUrl(route('dashboard'));
$item->setIcon('bi bi-house');
$item->setTarget('_blank');
$item->setVisible(true);
$item->setDisabled(false);
$item->setTitleVisible(true);
$item->addAttribute(['data-test' => 'main-dashboard-link']);
$item->addChild(MenuItem::make('Child', route('child')));
$item->addChildren([...]);
```


---

## Nested menus

A `MenuItem` can have child items.

### Example dropdown menu

```php
<?php

namespace App\Menus;

use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

class MainNav extends MenuInterface
{
    public function build(): static
    {
        $admin = MenuItem::make('Admin')
            ->addChildren([
                MenuItem::make('Users', route('users.index')),
                MenuItem::make('Roles', route('roles.index')),
            ]);

        $this->addChildren([
            MenuItem::make('Dashboard', route('dashboard')),
            $admin,
        ]);

        return $this;
    }
}
```


When an item has children:

- it is rendered as a dropdown trigger,
- its own URL is not shown,
- the Blade component renders its children inside a submenu.

That behavior comes from `showUrl()`:

- URLs are hidden for items with children,
- URLs are hidden for separators,
- URLs are hidden for disabled items.

---

## Separators

You can create separators with:

```php
MenuItem::makeSeparator()
```


Example:

```php
$admin = MenuItem::make('Admin')
    ->addChildren([
        MenuItem::make('Users', route('users.index')),
        MenuItem::makeSeparator(),
        MenuItem::make('Roles', route('roles.index')),
    ]);
```


In the Blade component, separators are rendered as:

```html
<hr class="dropdown-divider">
```


Also, the renderer skips separators when they would be:

- the first item,
- the last item,
- directly after another separator.

So it does some nice cleanup automatically.

---

## Icons

You can attach an icon class:

```php
MenuItem::make('Dashboard', route('dashboard'))
    ->setIcon('bi bi-house');
```


If an icon is set:

- the icon is rendered inside an `<i>` tag,
- the label is shown only if `isTitleVisible()` returns `true`.

Example with hidden text:

```php
MenuItem::make('Dashboard', route('dashboard'))
    ->setIcon('bi bi-house')
    ->setTitleVisible(false);
```


Useful for compact icon-only menu items.

---

## Visibility

Use `setVisible(false)` to hide an item:

```php
MenuItem::make('Admin', route('admin'))->setVisible(false);
```


The builder calls:

```php
$menu->removeHidden();
```


after building the menu.

That means hidden items are automatically removed before rendering.

### Parent visibility behavior

If a parent has children, `isVisible()` returns `true` only if at least one non-separator child is visible.

So if all child items are hidden, the parent disappears too. Very tidy.

---

## Disabled items

Disable an item like this:

```php
MenuItem::make('Coming Soon', '#')->setDisabled(true);
```


A disabled item:

- gets the `disabled` class in the Blade component,
- has no usable URL via `getUrl()`,
- is rendered with a `disabled` attribute.

Note: the `setDisabled(bool $disabled, bool $includeChildren = true)` signature includes `$includeChildren`, but the current implementation does not use that parameter to propagate the disabled state to children.

So documentation should treat it as unused for now.

---

## Custom HTML attributes

You can add arbitrary attributes to a menu item:

```php
MenuItem::make('Docs', 'https://example.com/docs')
    ->addAttribute([
        'rel' => 'noopener noreferrer',
        'data-track' => 'docs-link',
    ]);
```


These are merged into the anchor tag by the Blade component.

---

## Link targets

Use `setTarget()` for `_self`, `_blank`, or another valid target string:

```php
MenuItem::make('Docs', 'https://example.com/docs')
    ->setTarget('_blank');
```


The current implementation validates target values using this pattern:

```plain text
[a-zA-Z0-9\-_]+
```


So only simple alphanumeric, dash, and underscore target names are accepted.

If the target is invalid, an `InvalidArgumentException` is thrown.

---

## Query strings for all menu items

`MenuItem` provides:

```php
appendQuery(array $query): static
```


This recursively appends query parameters to all child URLs and the current item URL, where applicable.

Example:

```php
$menu = menu('MainNav')->appendQuery([
    'ref' => 'navbar',
]);
```


This is useful if you want to preserve filter state, tabs, or tracking data across menu links.

Note: it only applies to items where `showUrl()` is `true`.

---

## Route lookup for a menu item

Each item can attempt to resolve its URL to a Laravel route:

```php
$route = $item->getRoute();
```


This internally creates a request from the item URL and matches it against the router.

If the item has no usable URL, it returns `null`.

This can be useful if you want to compare menu items against route names or inspect route metadata in custom rendering logic.

---

## Rendering in Blade

The included component is:

```blade
<x-tk-base::navitem />
```


It expects a `MenuItem` and recursively renders child items.

### Example navigation

```blade
<ul class="navbar-nav me-auto mb-2 mb-sm-0">
    @foreach (menu('MainNav')->getChildren() as $item)
        <x-tk-base::navitem
            :item="$item"
            level="0"
            class="nav-item"
            submenu-class="dropdown-menu"
            link-class="nav-link"
        />
    @endforeach
</ul>
```


### Props supported by the component

- `item`
- `maxLevel` default: `2`
- `level` default: `0`
- `linkClass`
- `submenuClass`

### Rendering behavior

The component:

- adds `dropdown` if the item has children,
- adds `disabled` if the item is disabled,
- renders separators as `<hr class="dropdown-divider">`,
- renders dropdown toggles for parent items,
- recursively renders children using the same component.

It also prevents rendering nested dropdowns deeper than the configured `maxLevel`.

---

## Example: full Bootstrap 5 navbar

```blade
<header>
    <nav class="navbar navbar-expand-sm fixed-top mb-2 navbar-light bg-light shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                {{ config('app.name', 'Example') }}
            </a>

            <button
                class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarCollapse"
                aria-controls="navbarCollapse"
                aria-expanded="false"
                aria-label="Toggle navigation"
            >
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                    @foreach (menu('MainNav')->getChildren() as $item)
                        <x-tk-base::navitem
                            :item="$item"
                            level="0"
                            class="nav-item"
                            submenu-class="dropdown-menu"
                            link-class="nav-link"
                        />
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
```


---

## Example: menu with visibility, icons, separator, and dropdown

```php
<?php

namespace App\Menus;

use Tk\Menu\MenuInterface;
use Tk\Menu\MenuItem;

class MainNav extends MenuInterface
{
    public function build(): static
    {
        $admin = MenuItem::make('Admin')
            ->setIcon('bi bi-gear')
            ->addChildren([
                MenuItem::make('Users', route('users.index')),
                MenuItem::makeSeparator(),
                MenuItem::make('Settings', route('settings.index'))
                    ->setVisible(auth()->check()),
            ]);

        $this->addChildren([
            MenuItem::make('Dashboard', route('dashboard'))
                ->setIcon('bi bi-house'),
            MenuItem::make('Examples', route('examples.index')),
            $admin,
            MenuItem::make('Documentation', 'https://example.com/docs')
                ->setTarget('_blank')
                ->addAttribute(['rel' => 'noopener noreferrer']),
        ]);

        return $this;
    }
}
```


---

## Configuration

Menu builder namespaces are configured in:

```php
config/tkbase.php
```


Current package config:

```php
'menu_builders' => [
    'App\\Menus\\',
],
```


If you publish the package config, you can change or extend the namespaces searched by `MenuBuilder`.

For example:

```php
'menu_builders' => [
    'App\\Menus\\',
    'App\\Admin\\Menus\\',
],
```


Then:

```php
menu('Sidebar')
```


would search those namespaces in order until it finds a matching class.

---

## Service provider integration

The package registers a `Menu` alias in `TkBaseServiceProvider`, so the facade alias is available through Laravel’s alias loader.

That supports both:

```php
use Tk\Support\Facades\Menu;
```


and the helper:

```php
menu('MainNav')
```


---

## Practical recommendations

### Keep menu classes small and focused

A good pattern is one class per menu, for example:

- `MainNav`
- `AdminNav`
- `UserMenu`

### Use `route()` instead of hard-coded URLs

Prefer:

```php
MenuItem::make('Users', route('users.index'))
```


over:

```php
MenuItem::make('Users', '/users')
```


### Hide items with application logic in `build()`

Example:

```php
MenuItem::make('Admin', route('admin'))
    ->setVisible(auth()->user()?->is_admin ?? false);
```


### Use parent items for dropdowns only

Since a parent with children does not show its own URL, treat those items as containers rather than clickable links.

---

## Caveats in the current implementation

A few details worth knowing:

- `MenuBuilder` currently imports `Session`, but menu caching is commented out and not active.
- The builder constructs menu classes with `new $menuClass('_top')`, so your menu class inherits the `MenuItem` constructor behavior and receives `_top` as the root label.
- The root menu object is not itself intended for direct display.
- `setDisabled(..., $includeChildren = true)` does not currently cascade to children.
- `appendQuery()` does not distinguish between regular URLs and schemes like `mailto:`, anchors, or JavaScript URLs.

Nothing fatal — just useful to know before one of those details surprises you on a Friday afternoon.

---

## Quick reference

### Build a menu

```php
$menu = menu('MainNav');
```


or

```php
$menu = \Tk\Support\Facades\Menu::build('MainNav');
```


### Define a menu class

```php
class MainNav extends \Tk\Menu\MenuInterface
{
    public function build(): static
    {
        $this->addChildren([
            \Tk\Menu\MenuItem::make('Dashboard', route('dashboard')),
        ]);

        return $this;
    }
}
```


### Render in Blade

```blade
@foreach (menu('MainNav')->getChildren() as $item)
    <x-tk-base::navitem
        :item="$item"
        level="0"
        class="nav-item"
        submenu-class="dropdown-menu"
        link-class="nav-link"
    />
@endforeach
```


