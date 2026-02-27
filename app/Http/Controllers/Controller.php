<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;

abstract class Controller
{
    private string $pageTitle = '';

    /**
     * Set the page title and push a breadcrumb
     */
    public function setPageTitle(string $title): Controller
    {
        // push a breadcrumb to the stack
        $this->pageTitle = breadcrumbs()->push($title);

        // Make the page title accessible to all views
        View::share('pageTitle', $this->pageTitle);

        return $this;
    }

    public function getPageTitle(): string
    {
        return $this->pageTitle;
    }

}
