<?php

namespace Tk\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Breadcrumbs
{
    /**
     * Reset the breadcrumbs when a request contains a reset query param
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has(\Tk\Breadcrumbs\Breadcrumbs::CRUMB_RESET)) {
            // reset breadcrumbs
            \Tk\Support\Facades\Breadcrumbs::reset();

            // redirect back without the breadcrumb reset parameter
            Session::save();
            $url = $request->fullUrlWithoutQuery(\Tk\Breadcrumbs\Breadcrumbs::CRUMB_RESET);
            return redirect($url);
        }

        return $next($request);
    }
}
