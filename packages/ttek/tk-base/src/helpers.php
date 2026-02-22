<?php

if (!function_exists('menu')) {
    function menu(string $builder = '')
    {
        return \Tk\Menu\MenuBuilder::make()->build($builder)->menus();
    }
}

if (!function_exists('vd')) {
    function vd(): string
    {
        if (!config('app.debug')) return '';

        $vd = \Tk\Debug\VarDump::instance();
        $line = current(debug_backtrace());
        $path = str_replace($vd->getBasePath(), '', $line['file'] ?? '');
        $str = "\n";
        $str .= $vd->makeDump(func_get_args());
        $str .= sprintf('vd(%s) %s [%s];', implode(', ', $vd->getTypeArray(func_get_args())), $path, $line['line'] ?? 0) . "\n";
        \Illuminate\Support\Facades\Log::debug($str);
        return $str;
    }
}

if (!function_exists('vdd')) {
    function vdd(): string
    {
        if (!config('app.debug')) return '';

        $vd = \Tk\Debug\VarDump::instance();
        $line = current(debug_backtrace());
        $path = str_replace($vd->getBasePath(), '', $line['file'] ?? '');
        $str = "\n";
        $str .= $vd->makeDump(func_get_args(), true);
        $str .= sprintf('vdd(%s) %s [%s]', implode(', ', $vd->getTypeArray(func_get_args())), $path, $line['line'] ?? 0) . "\n";
        \Illuminate\Support\Facades\Log::debug($str);
        return $str;
    }
}

