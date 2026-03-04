# Breadcrumbs

Access the breadcrumb session object through the `\Tk\Support\Facades\Breadcrumbs` facade.

To get started, create/update the method `\App\Controller::setTitle()` to set the page title and push a breadcrumb:
```
abstract class Controller implements \Tk\Contracts\Http\Controller\BreadcrumbsController
{
    /**
     * Set the page title and push a breadcrumb
     */
    public function setTitle(string $title, bool $withCrumb = true): Controller
    {
        // push a breadcrumb to the stack
        if ($withCrumb) $title = Breadcrumbs::push($title);

        // (optional) Make the page title accessible to all views
        View::share('pageTitle', $title);

        return $this;
    }

}
```
Use the `\Tk\Contracts\Http\Controller\BreadcrumbsController` interface to add breadcrumbs method to your controllers.

For controllers that want to add a breadcrumb you can now call `$this->setTitle('Page Title')` within a controller.


Breadcrumbs can be reset by calling `Breadcrumbs::reset()`. To enable breadcrumbs reset and to dump the Breadcrmbs session on logout, 
add middleware to the `web` group. Global middleware must be added to the projects `app/bootstrap/app.php` file.

_Note: Requires a route named `logout`._
```
    ->withMiddleware(function (Middleware $middleware): void {
        // resets breadcrumb stack and redirects
        $middleware->appendToGroup('web', [Tk\Middleware\Breadcrumbs::class]);
    })
```

To create a reset url call `Breadcrumbs::getResetUrl($url)` that will add the reset param to the url.
Use the constant `Breadcrumbs::CRUMB_RESET` constant for the query param name to manually update your urls.


The Breadcrumbs stack is stored in the session and can be accessed via the `\Tk\Support\Facades\Breadcrumbs` facade.
View your ServiceProvider object to see how the Breadcrumbs Facade is registered. 


Render the breadcrumbs using the template in `resources/views/components/breadcrumbs.blade.php`.
```
    <main class="pt-5">
    
        <x-tk-base::breadcrumbs />

        <div class="container">
            {{ $slot }}
        </div>

    </main>
```



