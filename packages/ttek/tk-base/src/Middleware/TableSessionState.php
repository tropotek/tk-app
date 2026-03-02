<?php

namespace Tk\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tk\Table\Table;

class TableSessionState
{
    /**
     * Save all table query params to the session.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->has(Table::QUERY_ID)) {

            $tid = trim($request->input(Table::QUERY_ID));
            if (empty($tid)) {
                return $next($request);
            }

            $sid = Table::SESSION_PRE.$tid;
            $state = [];
            if ($request->session()->has($sid)) {
                $state = $request->session()->get($sid);
            }

            $tableParams = collect($request->query())->filter(function ($value, $key) use($tid) {
                return Str::startsWith($key, $tid.'_');
            });

            $query = [
                Table::QUERY_ID => null,
            ];
            foreach ($tableParams as $key => $value) {
                $state[$key] = $value;
                $query[$key] = null;
            }

            if ($request->has(Table::makeKey($tid, Table::QUERY_RESET))) {
                $request->session()->forget($sid);
            } else {
                $request->session()->put($sid, $state);
                $request->session()->save();
            }

            // remove all table query params
            vd('REdirecting');
            $url = trim($request->fullUrlWithQuery($query), '?');
            return redirect($url);
        }

        return $next($request);
    }
}
