<?php

namespace Tk\Support\Facades;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \Tk\Breadcrumbs\Breadcrumbs push(string $title, ?string $url = null)
 * @method static RedirectResponse|Redirector pop()
 * @method static string lastUrl()
 * @method static int count()
 * @method static \Tk\Breadcrumbs\Breadcrumbs reset()
 * @method static string getHomeTitle()
 * @method static \Tk\Breadcrumbs\Breadcrumbs setHomeTitle(string $homeTitle)
 * @method static string getHomeUrl()
 * @method static \Tk\Breadcrumbs\Breadcrumbs setHomeUrl(string $homeUrl)
 * @method static array toArray()
 * @method static string __toString()
 */
class Breadcrumbs extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return \Tk\Breadcrumbs\Breadcrumbs::class;
    }

}
