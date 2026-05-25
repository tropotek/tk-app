<?php

use Illuminate\Support\Facades\Log;
use Tk\Breadcrumbs\Breadcrumbs;
use Tk\Debug\VarDump;
use Tk\Menu\Menu;
use Tk\Support\Facades\MenuBuilder;

if (! function_exists('menu')) {
    function menu(string $builder = ''): Menu
    {
        return MenuBuilder::compileMenu($builder);
    }
}

if (! function_exists('breadcrumbs')) {
    function breadcrumbs(): Breadcrumbs
    {
        return app(Breadcrumbs::class);
    }
}

//  ---------    Debug functions -------------

if (! function_exists('vd')) {
    function vd(): string
    {
        if (! config('app.debug')) {
            return '';
        }

        $vd = VarDump::instance();
        $line = current(debug_backtrace());
        $path = str_replace($vd->getBasePath(), '', $line['file'] ?? '');
        $str = "\n";
        $str .= $vd->makeDump(func_get_args());
        $str .= sprintf('vd(%s) %s [%s];', implode(', ', $vd->getTypeArray(func_get_args())), $path, $line['line'] ?? 0)."\n";
        Log::debug($str);

        return $str;
    }
}

if (! function_exists('vdd')) {
    function vdd(): string
    {
        if (! config('app.debug')) {
            return '';
        }

        $vd = VarDump::instance();
        $line = current(debug_backtrace());
        $path = str_replace($vd->getBasePath(), '', $line['file'] ?? '');
        $str = "\n";
        $str .= $vd->makeDump(func_get_args(), true);
        $str .= sprintf('vdd(%s) %s [%s]', implode(', ', $vd->getTypeArray(func_get_args())), $path, $line['line'] ?? 0)."\n";
        Log::debug($str);

        return $str;
    }
}
