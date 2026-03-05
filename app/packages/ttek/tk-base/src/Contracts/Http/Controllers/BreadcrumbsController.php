<?php

namespace Tk\Contracts\Http\Controllers;

interface BreadcrumbsController
{

    /**
     * Set the page name for the controller and push a Crumb to the Breadcrumbs stack
     * EG:
     * ```
     * public function setPageName(string $title, bool $withCrumb = true): Controller
     * {
     *      // push a crumb to the stack
     *      if ($withCrumb) $title = Breadcrumbs::push($title);
     *
     *      // set $pageTitle to all views
     *      View::share(self::PAGE_TITLE, $title);
     *
     *      return $this;
     * }
     * ```
     */
    public function setPageName(string $pageName, bool $withCrumb = true): static;

}
