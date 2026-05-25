<?php

namespace Tk\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;
use Tk\Breadcrumbs\Breadcrumbs;

class ResetBreadcrumbs
{
    /**
     * Reset the breadcrumbs when a request contains a reset query param
     * Dump Breadcrumbs session on logout
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // remove breadcrumbs on logout
        if ($request->routeIs('logout') || $request->routeIs('login')) {
            Session::forget(Breadcrumbs::class);
        }

        // reset breadcrumbs on request
        if ($request->has(Breadcrumbs::CRUMB_RESET)) {
            \Tk\Support\Facades\Breadcrumbs::reset();
            Session::save();
            $url = $request->fullUrlWithoutQuery(Breadcrumbs::CRUMB_RESET);

            return redirect($url);
        }

        return $next($request);
    }
}
