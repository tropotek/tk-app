<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use Tk\Contracts\Http\Controllers\BreadcrumbsController;
use Tk\Support\Facades\Breadcrumbs;

abstract class Controller implements BreadcrumbsController
{
    // the page title view property
    const string TITLE = 'title';

    private string $title = '';


    /**
     * Set the page title and push a breadcrumb
     */
    public function setTitle(string $title, bool $withCrumb = true): static
    {
        $this->title = $title;
        // push a breadcrumb to the stack
        if ($withCrumb) {
            $this->title = Breadcrumbs::push($this->title);
        }

        // set $pageTitle to all views
        View::share(self::TITLE, $this->title);

        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

}
