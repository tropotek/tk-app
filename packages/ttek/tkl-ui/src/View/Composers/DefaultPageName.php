<?php

namespace Tk\View\Composers;

use Illuminate\Support\Facades\View;
use Tk\Breadcrumbs\Breadcrumbs;

class DefaultPageName
{
    /**
     * If a controller has not set the page name,
     * then set a default based on the route basename.
     */
    public function compose(\Illuminate\View\View $view): void
    {
        if (View::getShared()[Breadcrumbs::PAGE_NAME] ?? false) return;

        $basename = basename(request()->path()) ?: '';
        $defaultName = ucwords(str_replace(['/', '_'], ' ', strtolower($basename)));

        View::share(Breadcrumbs::PAGE_NAME, $defaultName);
    }
}
