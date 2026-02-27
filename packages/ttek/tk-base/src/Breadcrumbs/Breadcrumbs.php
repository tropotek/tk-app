<?php

namespace Tk\Breadcrumbs;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;

final class Breadcrumbs
{
    /**
     * query string to reset breadcrumbs
     */
    const string CRUMB_RESET  = '_cr';

    protected Collection  $stack;
    protected string      $homeTitle     = 'Dashboard';
    protected string      $homeUrl       = '/';


    public function __construct()
    {
        $this->stack = collect();
    }

    /**
     * $title can include breadcrumb name and display name
     * internal breadcrumb name used to remove duplicates/loops.
     * e.g: "crumb name|Page Title"
     *
     * Returns a crumb array {"name": 'crumb name', "title": 'Page Title', "url": 'url'}
     * @return array<string,string>
     */
    protected function createCrumb(string $title, string $url): array
    {
        [$name, $title] = array_map('trim', explode('|', $title.'|'));
        if (!$title) $title = $name;
        $title = ucwords(strip_tags($title));
        return compact('name', 'title', 'url');
    }

    /**
     * push a crumb to the stack returning the page title
     */
    public function push(string $title, ?string $url = null): string
    {
        $url = $url ?? request()->getRequestUri();
        if ($url === $this->getHomeUrl()) {
            $this->reset();
            return $this->getHomeTitle();
        }

        $crumb = $this->createCrumb($title, $url);

        // look for this page already in the breadcrumbs
        // if found rewind breadcrumbs to before this page
        $this->stack = $this->stack->takeWhile(fn($c) => $c['name'] !== $crumb['name']);

        // TODO mm
        // check for the top breadcrumb (previous page) matching the
        // referring page (where we just came from)
        // if they match change the query string to match in case
        // the referring page changed its URL dynamically
        // with the Javascript history API
//        $referer = $_SERVER['HTTP_REFERER'] ?? '';
//        if ($referer && count($bc['urls']) > 0) {
//            $top_bc = array_pop($bc['urls']);
//            $referer_parts = parse_url($referer);
//            if (parse_url($top_bc, PHP_URL_PATH) == ($referer_parts['path'] ?? '')) {
//                $top_bc = $referer_parts['path'] ?? '';
//                if ($referer_parts['query'] ?? '') $top_bc .= ("?" . $referer_parts['query']);
//                if ($referer_parts['fragment'] ?? '') $top_bc .= ("#" . $referer_parts['fragment']);
//            }
//            array_push($bc['urls'], $top_bc);
//        }

	    // add this page to the top of the breadcrumbs
        $this->stack->push($crumb);

        // return page display title
        return $crumb['title'];
    }

    /**
     * Pop the last crumb from the stack (prev if current url already added to stack)
     * Returns a redirect response object
     * @return RedirectResponse|Redirector
     */
    public function pop(): RedirectResponse|Redirector
    {
        if (!$this->count()) return redirect($this->getHomeUrl());

        $url = $this->getHomeUrl();
        $currUrl = request()->getRequestUri();

        do {
            $crumb = $this->stack->pop();
            if ($crumb) $url = $crumb['url'];
        } while ($url == $currUrl);

        return redirect($url);
    }

    /**
     * Return the last URL in the breadcrumb stack before the current page
     */
    public function lastUrl(): string
    {
        if (!$this->count()) return $this->getHomeUrl();

        $url = $this->getHomeUrl();
        $currUrl = request()->getRequestUri();
        $stack = clone $this->stack;

        do {
            $crumb = $stack->pop();
            if ($crumb) $url = $crumb['url'];
        } while ($url == $currUrl);

        return $url;
    }

    public function count(): int
    {
        return $this->stack->count();
    }

    /**
     * reset crumb stack
     */
    public function reset(): self
    {
        $this->stack = collect();
        return $this;
    }

    public function getHomeTitle(): string
    {
        return $this->homeTitle;
    }

    public function setHomeTitle(string $homeTitle): self
    {
        $this->homeTitle = $homeTitle;
        return $this;
    }

    public function getHomeUrl(): string
    {
        return $this->homeUrl;
    }

    public function setHomeUrl(string $homeUrl): self
    {
        $this->homeUrl = $homeUrl;
        return $this;
    }

    /**
     * Add a reset breadcrumb query string to a url
     */
    public function getResetUrl(string $url): string
    {
        return url()->query($url, [self::CRUMB_RESET => '1']);
    }

    /**
     * @return array<string,string>
     */
    public function toArray(): array
    {
        $homeCrumb = [
            'name' => $this->getHomeTitle(),
            'title' => $this->getHomeTitle(),
            'url' => $this->getHomeUrl(),
        ];
        $crumbs = $this->stack->toArray();
        array_unshift($crumbs, $homeCrumb);
        return $crumbs;
    }

    public function __toString(): string
    {
        $str = $this->homeTitle;
        foreach ($this->stack as $crumb) {
            $str .= ' > ' . $crumb['title'];
        }
        return $str;
    }
}
