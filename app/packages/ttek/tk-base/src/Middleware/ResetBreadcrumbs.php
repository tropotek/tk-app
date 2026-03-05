<?php

namespace Tk\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class ResetBreadcrumbs
{
    /**
     * Reset the breadcrumbs when a request contains a reset query param
     * Dump Breadcrumbs session on logout
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // remove breadcrumbs on logout
        if ($request->routeIs('logout') || $request->routeIs('login')) {
            Session::forget(\Tk\Breadcrumbs\Breadcrumbs::class);
        }

        // reset breadcrumbs on request
        if ($request->has(\Tk\Breadcrumbs\Breadcrumbs::CRUMB_RESET)) {
            \Tk\Support\Facades\Breadcrumbs::reset();
            Session::save();
            $url = $request->fullUrlWithoutQuery(\Tk\Breadcrumbs\Breadcrumbs::CRUMB_RESET);
            return redirect($url);
        }

        return $next($request);
    }
}
