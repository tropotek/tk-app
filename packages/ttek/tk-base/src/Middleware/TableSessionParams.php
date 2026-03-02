<?php

namespace Tk\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tk\Table\Table;

class TableSessionParams
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

            $tableParams = collect($request->query())->filter(function ($value, $key) use($tid) {
                return Str::startsWith($key, $tid.'_');
            });

            // save and redirect if table id params exist
            if (count($tableParams)) {
                $sid = Table::SESSION_PRE.$tid;
                $params = [];
                if ($request->session()->has($sid)) {
                    $params = $request->session()->get($sid);
                }

                $query = [];
                foreach ($tableParams as $key => $value) {
                    $params[$key] = $value;
                    $query[$key] = null;
                }

                if ($request->has(Table::makeKey($tid, Table::QUERY_RESET))) {
                    $request->session()->forget($sid);
                } else {
                    $request->session()->put($sid, $params);
                    $request->session()->save();
                }

                $query[Table::QUERY_ID] = null;
                // remove all table query params
                $url = trim($request->fullUrlWithQuery($query), '?');
                return redirect($url);
            }
        }

        return $next($request);
    }
}
