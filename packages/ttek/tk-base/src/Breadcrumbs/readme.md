# Breadcrumbs

## What it does

`Tk\Breadcrumbs\Breadcrumbs` manages a breadcrumb trail stored in the **session**.

It is designed to:

- keep track of page navigation,
- avoid duplicate breadcrumb loops,
- provide a “back to previous breadcrumb” URL or redirect,
- reset the breadcrumb stack when needed,
- render breadcrumbs in Blade through a reusable component.

---

## Related files

These are the main files involved:

- `app/packages/ttek/tk-base/src/Breadcrumbs/Breadcrumbs.php`
- `app/packages/ttek/tk-base/src/Support/Facades/Breadcrumbs.php`
- `app/packages/ttek/tk-base/src/Contracts/Http/Controllers/BreadcrumbsController.php`
- `app/packages/ttek/tk-base/src/Middleware/ResetBreadcrumbs.php`
- `app/packages/ttek/tk-base/resources/views/components/breadcrumbs.blade.php`
- `app/packages/ttek/tk-base/src/TkBaseServiceProvider.php`

And in the application setup:

- `app/bootstrap/app.php`

---

## How the service is registered

The package service provider binds the breadcrumb service into Laravel’s container and exposes a facade alias.

### Binding behavior

In `TkBaseServiceProvider`:

- authenticated users get a breadcrumb instance with:
    - home title: `Dashboard`
    - home URL: `route('dashboard')`
- guests get:
    - home title: `Home`
    - home URL: `route('home')`

So in normal use, you usually don’t instantiate `Breadcrumbs` manually. You use the facade:

```php
use Tk\Support\Facades\Breadcrumbs;
```


---

## Main API

## 1. Push a breadcrumb

Adds the current page to the breadcrumb stack.

```php
Breadcrumbs::push('Users');
```


This returns the **display title** of the breadcrumb.

### Optional title format

The class supports this syntax:

```php
Breadcrumbs::push('internal-name|Visible Page Title');
```


- left side = internal breadcrumb name
- right side = title shown to the user

This is useful because the internal name is used to detect duplicates and rewind the stack.

Example:

```php
Breadcrumbs::push('users.index|Users');
```


If you revisit the same named crumb later, the stack is trimmed back to that point instead of duplicating it.

---

## 2. Pop to previous breadcrumb

Redirects to the previous breadcrumb URL:

```php
return Breadcrumbs::pop();
```


This returns a Laravel redirect response.

If the stack is empty, it redirects to the configured home URL.

---

## 3. Get the previous breadcrumb URL

If you need the previous breadcrumb URL without redirecting:

```php
$url = Breadcrumbs::lastUrl();
```


Useful for buttons or links:

```php
return redirect(Breadcrumbs::lastUrl());
```


---

## 4. Reset the breadcrumb stack

```php
Breadcrumbs::reset();
```


This clears all breadcrumbs except the conceptual home page.

---

## 5. Build a reset URL

To generate a URL that resets breadcrumbs first:

```php
$url = Breadcrumbs::getResetUrl(route('dashboard'));
```


This appends the reset query parameter automatically.

The query parameter name is defined by:

```php
\Tk\Breadcrumbs\Breadcrumbs::CRUMB_RESET
```


Current value:

```php
_cr
```


---

## 6. Convert breadcrumbs to an array

```php
$items = Breadcrumbs::toArray();
```


This returns an array like:

```php
[
    'Dashboard' => '/dashboard',
    'Users' => '/users',
    'Edit User' => '/users/5/edit',
]
```


That array is what the Blade component uses for rendering.

---

## 7. String output

```php
(string) Breadcrumbs::getFacadeRoot()
```


The class supports `__toString()` and produces something like:

```plain text
Dashboard > Users > Edit User
```


This is mostly useful for debugging.

---

## Recommended controller integration

The intended pattern is to make your base controller support breadcrumbs and expose the current page title to views.

The package provides this interface:

```php
Tk\Contracts\Http\Controllers\BreadcrumbsController
```


A typical implementation looks like this:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Contracts\Http\Controllers\BreadcrumbsController as BreadcrumbsControllerContract;

abstract class Controller extends BaseController implements BreadcrumbsControllerContract
{
    public function setPageName(string $pageName, bool $withCrumb = true): static
    {
        if ($withCrumb) {
            $pageName = Breadcrumbs::push($pageName);
        }

        View::share('pageName', $pageName);

        return $this;
    }
}
```


Then in a controller action:

```php
<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function index()
    {
        $this->setPageName('users.index|Users');

        return view('pages.users.index');
    }

    public function edit(int $id)
    {
        $this->setPageName('users.edit|Edit User');

        return view('pages.users.edit', [
            'id' => $id,
        ]);
    }
}
```


---

## Using breadcrumbs without pushing to the stack

Sometimes you want to set the page title for the view but **not** add a breadcrumb.

That’s what the `$withCrumb` flag is for:

```php
$this->setPageName('Reports', false);
```


Useful for modal-like pages, partial flows, or screens that shouldn’t alter navigation history.

---

## Middleware setup

The middleware responsible for reset behavior is:

```php
Tk\Middleware\ResetBreadcrumbs
```


To add the middleware to your project edit the `app/bootstrap/app.php` file.

```
    ->withMiddleware(function (Middleware $middleware): void {
        ...
        
        // resets breadcrumb stack and redirects
        $middleware->appendToGroup('web', [ResetBreadcrumbs::class]);
        
        ...
    })
```

Its behavior:

- if the route is `logout` or `login`, it clears the breadcrumb session,
- if the request contains the reset query parameter (`_cr`), it:
    - resets breadcrumbs,
    - removes the query parameter from the URL,
    - redirects to the cleaned URL.

### Important note

This behavior assumes your application uses route names like:

- `login`
- `logout`

If those names change, the automatic clearing on login/logout will no longer match.

---

## Rendering in Blade

The package includes a Blade component:

```bladehtml
<x-tk-base::breadcrumbs />
```


A typical layout usage:

```bladehtml
<main class="pt-5">
    <x-tk-base::breadcrumbs />

    <div class="container">
        {{ $slot }}
    </div>
</main>
```


### How the component works

The component view:

- reads breadcrumb items from `Breadcrumbs::toArray()`,
- renders each item in a Bootstrap-style `<ol class="breadcrumb">`,
- shows the last breadcrumb as plain text,
- renders earlier items as links.

So once your controllers push breadcrumbs, the component just works.

---

## Title parsing behavior

`parseTitle()` supports a pipe-delimited format:

```php
'name|Display Title'
```


Examples:

```php
Breadcrumbs::push('users.index|Users');
Breadcrumbs::push('users.edit|Edit User');
Breadcrumbs::push('Dashboard');
```


Behavior:

- if no `|` is present, the same value is used for both internal name and display title,
- HTML tags are stripped from the display title,
- the display title is normalized with `ucwords()`.

So:

```php
Breadcrumbs::push('users.index|edit user');
```

becomes display title:

```plain text
Edit User
```


---

## Duplicate and loop handling

One nice feature of this class is that it tries to avoid breadcrumb loops.

When you push a breadcrumb:

- it checks whether a crumb with the same internal name already exists,
- if found, it trims the stack back to before that crumb,
- then it pushes the new one.

That means flows like this:

```plain text
Dashboard > Users > Edit User > Users
```


can collapse sensibly instead of endlessly repeating.

This is why using stable internal names such as `users.index` and `users.edit` is a good idea.

---

## URL behavior

Breadcrumb URLs are normalized internally to:

- path
- optional query string

The home URL is also normalized.

Also, when pushing a crumb, the class uses the current request URI by default.

One subtle detail: it also checks the HTTP `referer` header and may update the previous crumb URL if the path matches. This helps when the previous page changed its query string dynamically.

Tiny breadcrumb goblin, surprisingly thoughtful.

---

## Common usage patterns

## Basic page

```php
public function index()
{
    $this->setPageName('Dashboard');

    return view('pages.dashboard');
}
```


## Named breadcrumb with nice display title

```php
public function index()
{
    $this->setPageName('ideas.index|Ideas');

    return view('pages.ideas.index');
}
```


## Detail page

```php
public function show(int $id)
{
    $this->setPageName('ideas.show|Idea Details');

    return view('pages.ideas.show', ['id' => $id]);
}
```


## Back button using breadcrumb history

```bladehtml
<a href="{{ \Tk\Support\Facades\Breadcrumbs::lastUrl() }}" class="btn btn-secondary">
    Back
</a>
```


## Reset breadcrumb history when entering a new flow

```bladehtml
<a href="{{ \Tk\Support\Facades\Breadcrumbs::getResetUrl(route('dashboard')) }}">
    Start over
</a>
```


---

## Best practices

### Use stable internal names

Prefer:

```php
$this->setPageName('users.index|Users');
```


instead of only:

```php
$this->setPageName('Users');
```


Why? Because duplicate detection works better with explicit internal names.

---

### Keep display titles human-readable

Use titles your users should actually see:

```php
$this->setPageName('orders.edit|Edit Order');
```


---

### Push breadcrumbs from controllers, not views

The class is request/session-driven and belongs to navigation flow logic, so controllers are the cleanest place to manage it.

---

### Render in a shared layout

Put the Blade component in your main page wrapper or layout so every page consistently shows the current breadcrumb trail.

---

## Limitations / things to be aware of

- Breadcrumbs are **session-based**, so they are per-user session.
- Automatic cleanup depends on route names like `login` and `logout`.
- If you skip calling `setPageName()` or `Breadcrumbs::push()`, the trail won’t grow for that page.
- `push()` uses the current request URI by default, so unusual routing or client-side navigation patterns may need special handling.
- The component renders titles with `{!! $title !!}`. Since the class strips tags in `parseTitle()`, that reduces risk, but breadcrumb titles should still come from trusted app code, not raw user input.

---

## Minimal end-to-end example

### Base controller

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Contracts\Http\Controllers\BreadcrumbsController as BreadcrumbsControllerContract;

abstract class Controller extends BaseController implements BreadcrumbsControllerContract
{
    public function setPageName(string $pageName, bool $withCrumb = true): static
    {
        if ($withCrumb) {
            $pageName = Breadcrumbs::push($pageName);
        }

        View::share('pageName', $pageName);

        return $this;
    }
}
```


### Page controller

```php
<?php

namespace App\Http\Controllers;

class IdeasController extends Controller
{
    public function index()
    {
        $this->setPageName('ideas.index|Ideas');

        return view('pages.ideas.index');
    }

    public function show(int $id)
    {
        $this->setPageName('ideas.show|Idea Details');

        return view('pages.ideas.show', ['id' => $id]);
    }
}
```


### Layout

```bladehtml
<main class="pt-5">
    <x-tk-base::breadcrumbs />

    <div class="container">
        {{ $slot }}
    </div>
</main>
```


---

## Quick reference

### Facade

```php
use Tk\Support\Facades\Breadcrumbs;
```


### Methods

```php
Breadcrumbs::push(string $pageName, ?string $url = null);
Breadcrumbs::pop();
Breadcrumbs::lastUrl();
Breadcrumbs::reset();
Breadcrumbs::count();
Breadcrumbs::isEmpty();
Breadcrumbs::getHomeTitle();
Breadcrumbs::getHomeUrl();
Breadcrumbs::getResetUrl(string $url);
Breadcrumbs::toArray();
Breadcrumbs::parseTitle(string $pageName);
```


### Blade

```bladehtml
<x-tk-base::breadcrumbs />
```


### Middleware

```php
Tk\Middleware\ResetBreadcrumbs
```
