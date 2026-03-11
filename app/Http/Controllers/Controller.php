<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Tk\Contracts\Http\Controllers\BreadcrumbsController;
use Tk\Support\Facades\Breadcrumbs;

abstract class Controller implements BreadcrumbsController
{
    // the page name view property
    const string PAGE_NAME = 'pageName';


    /**
     * Set the page title and push a breadcrumb
     */
    public function setPageName(string $pageName, bool $withCrumb = true): static
    {
        // push a breadcrumb to the stack
        if ($withCrumb) {
            $pageName = Breadcrumbs::push($pageName);
        }

        // set for all views
        View::share(self::PAGE_NAME, $pageName);

        return $this;
    }

}
