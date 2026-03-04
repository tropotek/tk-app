<?php

namespace Tk\Contracts\Http\Controllers;

interface BreadcrumbsController
{

    /**
     * Set the page title for the controller and push a Crumb to the Breadcrumbs stack
     * EG:
     * ```
     * public function setTitle(string $title, bool $withCrumb = true): Controller
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
    public function setTitle(string $title, bool $withCrumb = true): static;

}
