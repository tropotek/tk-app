# Breadcrumbs

Access the breadcrumb object through the `\Tk\Support\Facades\Breadcrumbs` facade.

Breadcrumbs are stored in the session.

To get started, create a method in the `\App\Controller` class to push a crumb and receive the page title:
```
abstract class Controller
{
    /**
     * Set the page title and push a breadcrumb
     */
    public function setPageTitle(string $title): Controller
    {
        // push a breadcrumb to the stack
        $pageTitle = breadcrumbs()->push($title);

        // (optional) Make the page title accessible to all views
        View::share('pageTitle', $pageTitle);

        return $this;
    }

}
```

Now for any request where you want to set a breadcrumb, call the `$this->setPageTitle('Page Title)` method within the controller methods.

Breadcrumbs can be reset by calling `Breadcrumbs::reset()`. If you need the ability to reset breadcrumbs via a request, 
we need to add middleware to the `web` group. Global middleware must be added to the `app/bootstrap/app.php` file.
```
    ->withMiddleware(function (Middleware $middleware): void {
        // resets breadcrumb stack and redirects
        $middleware->appendToGroup('web', [Tk\Middleware\Breadcrumbs::class]);
    })
```

If you want to create a reset url call `Breadcrumbs::getResetUrl($url)` to add the breadcrumbs reset param to the url.
Use the constant `Breadcrumbs::CRUMB_RESET` to manually update your urls.


To render the breadcrumbs, use the template in `resources/views/components/breadcrumbs.blade.php`.
```
    <main class="pt-5">
    
        <x-tk-base::breadcrumbs />

        <div class="container">
            {{ $slot }}
        </div>

    </main>
```



