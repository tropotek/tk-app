<?php

namespace Tk\Breadcrumbs;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Collection;


final class Crumb
{
    public function __construct(
        public string $name,
        public string $title,
        public string $url
    ) {}
}

final class Breadcrumbs
{
    /**
     * query string to reset breadcrumbs
     */
    const string CRUMB_RESET  = '_cr';

    protected Collection  $stack;
    protected string      $homeTitle     = '';
    protected string      $homeUrl       = '';

    public function __construct(string $homeTitle = 'Dashboard', string $homeUrl = '/')
    {
        $this->stack = collect();
        $this->homeTitle = $homeTitle;
        $this->homeUrl = $homeUrl;
    }

    /**
     * $title can include breadcrumb name and display name
     * internal breadcrumb name used to remove duplicates/loops.
     * e.g: "crumb name|Page Title"
     *
     * @return Crumb
     */
    protected function createCrumb(string $title, string $url): Crumb
    {
        [$name, $title] = array_map('trim', explode('|', $title.'|'));
        if (!$title) $title = $name;
        $title = ucwords(strip_tags($title));
        return new Crumb($name, $title, $url);
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
        $this->stack = $this->stack->takeWhile(fn($c) => $c->name !== $crumb->name);

        // check for the top breadcrumb (previous page) matching the
        // referring page (where we just came from)
        // if they match change the query string to match in case
        // the referring page changed its URL dynamically
        // with the Javascript history API
        $referer = request()->header('referer') ?? '';
        if ($referer && !$this->isEmpty()) {
            $topBc = $this->stack->pop();
            $refererParts = parse_url($referer);
            if (parse_url($topBc->url, PHP_URL_PATH) == ($refererParts['path'] ?? '')) {
                $topBc->url = $refererParts['path'] ?? '';
                if ($refererParts['query'] ?? '') $topBc->url .= ("?" . $refererParts['query']);
                if ($refererParts['fragment'] ?? '') $topBc->url .= ("#" . $refererParts['fragment']);
            }
            $this->push($topBc->title, $topBc->url);
        }

	    // add this page to the top of the breadcrumbs
        $this->stack->push($crumb);

        // return page display title
        return $crumb->title;
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
            if ($crumb) $url = $crumb->url;
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
            if ($crumb) $url = $crumb->url;
        } while ($url == $currUrl);

        return $url;
    }

    public function count(): int
    {
        return $this->stack->count();
    }

    /**
     * The crumb stack is empty if there are no crumbs
     * The home url is excluded from the count
     */
    public function isEmpty(): bool
    {
        return $this->count() == 0;
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

    public function getHomeUrl(): string
    {
        return $this->homeUrl;
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
        return
            [$this->getHomeTitle() => $this->homeUrl] +
            array_column($this->stack->toArray(), 'url', 'title');
    }

    public function __toString(): string
    {
        $str = $this->homeTitle;
        foreach ($this->stack as $crumb) {
            $str .= ' > ' . $crumb->title;
        }
        return $str;
    }
}
