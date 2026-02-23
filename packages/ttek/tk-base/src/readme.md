# TK Base Package


## VarDump Debug logger
Use this to send messages to the debug log file. Send strings, objects, and arrays.
The output will show booleans and null values so you can identify them easily.
Usage:
```php
// view a dump of the entire object in the debug bar messages:
vd($object);
// use this to see the output along with the trace of the execution:
vdd($object);
```



## Form Builder

See the [Form Readme](./resources/views/components/form/readme.md) for an example of how to use form templates.


## Menu Builder

The menu builder can be used to generate navbar menus. Use the helper function `menu($builder)` to generate the menu.

By default, the menu builder looks in the `/app/Menus` folder for menu class files. This can be changed in the 
`tkbase.php` config file.
To change the location of the menu class files, copy the `config/tkbase.php` to your app config folder and change the 
`menu_builders` value using:
```
$ php artisan vendor:publish --provider="Tk\\PackageServiceProvider" --force
```

To get a menu using the `menu($builder)` helper function, use the base classname of the menu class.
For example the following will look for the class `App\Menus\MainNav` and return the generated menu:
```bladehtml
{{ menu('MainNav') }}
```

Currently, there is a bootstrap 5 navbar menu builder template included at `./resources/views/components/menu/bootstrap5.blade.php`.
Here is one possible implementation using a menu builder class `App\Menus\NavBar`:
```bladehtml
<header>
    <nav class="navbar navbar-expand-sm fixed-top mb-2 navbar-light bg-light shadow-sm">
        <div class="{{ config('app.resources.layout', 'container') }}">
            <a class="navbar-brand" href="/">{{ config('app.name', 'Example') }}</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse"
                    aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">
                <ul class="navbar-nav me-auto mb-2 mb-sm-0">
                    @foreach (menu('NavBar') as $item)
                        {{-- Pass initial classes to the component --}}
                        <x-tk-base::menu.bootstrap5-navitem :item="$item" level="0" class="nav-item" submenu-class="dropdown-menu" link-class="nav-link" />
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
</header>
```



## Util Classes

This folder contains useful classes and methods that can be used in your own packages.

