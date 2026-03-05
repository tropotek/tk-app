<?php

namespace Tk\View\Composers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class DefaultPageName
{
    /**
     * If a controller has not set the page name,
     * then set a default based on the route basename.
     */
    public function compose(\Illuminate\View\View $view): void
    {
        if (View::getShared()[Controller::PAGE_NAME] ?? false) return;

        $basename = basename(request()->path()) ?: '';
        $defaultName = ucwords(str_replace(['/', '_'], ' ', strtolower($basename)));

        View::share(Controller::PAGE_NAME, $defaultName);
    }
}
