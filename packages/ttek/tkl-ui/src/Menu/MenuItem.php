<?php
namespace Tk\Menu;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\View\ComponentAttributeBag;
use InvalidArgumentException;

/**
 * Stores required attributes and logic for a navigation menu item.
 *
 * Example:
 * ```
 * $menu = MenuItem::make('Dashboard')
 *     ->setUrl('/dashboard')
 *     ->setIcon('o-home-2-window-2x2')
 *     ->addChild(
 *         MenuItem::make('Users')->setUrl('/users')
 * );
 * ```
 *
 * @source https://dev.to/nasrulhazim/building-dynamic-and-maintainable-menus-in-laravel-ba0
 */
class MenuItem
{
    const string SEPARATOR = '---';

    private string $label;
    private string $url = '';
    private string $icon = '';
    private string $target = '_self';
    private bool $visible = true;
    private bool $disabled = false;
    private bool $titleVisible = true;
    private ComponentAttributeBag $attributes;
    private array $children = [];
    private array $query = [];


    public function __construct(string $label, string|Route $url = '')
    {
        $this->attributes = new ComponentAttributeBag();
        $this->setLabel($label);
        if ($url) {
            $this->setUrl($url);
        }
    }

    public static function make(string $label, string|Route $url = ''): self
    {
        return new self($label, $url);
    }

    public static function makeSeparator(): self
    {
        static $id = 0;
        return self::make(self::SEPARATOR . $id++);
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getLabel(): string
    {
        if ($this->isSeparator()) return '';
        return $this->label;
    }

    /**
     * Set the URL for the menu item.
     * Accepts a string URL or a Route instance.
     * If a string is passed in, it will attempt to find a matching Route from a route name or URI.
     * If a Route is found or passed in, the URL will be set to the Route URI
     *   and the visible flag will be set based on middleware and permissions.
     * If no Route is found or passed, the URL will be set to the string value.
     */
    public function setUrl(string|Route $url): self
    {
        $route = ($url instanceof Route) ? $url : null;

        if (is_string($url) && !empty($url)) {
            if ($this->isUrl($url)) {
                try {
                    // Try finding a Route from its URL
                    $request = Request::create($url);
                    $route = app('router')->getRoutes()->match($request);
                } catch (\Exception $e) { }
            } else {
                // Find the Route from its name or throw an error
                $route = app('router')->getRoutes()->getByName($url);
            }
        }

        if ($route) {
            $url = $route->uri();

            // todo: check middleware and permission and set visible value ??

        }

        $this->url = $url;
        return $this;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Check if a string is a valid URL or path
     */
    protected function isUrl(string|Route $value): bool
    {
        return match(true) {
            ($value instanceof Route) => true,
            filter_var($value, FILTER_VALIDATE_URL) => true,
            str_starts_with($value, '/') => true,
            default => false
        };
    }

    public function getAttributes(): ComponentAttributeBag
    {
        return $this->attributes;
    }

    public function addAttribute(array $attrs): self
    {
        $this->attributes = $this->attributes->merge($attrs);
        return $this;
    }

    public function setTarget(string $target): self
    {
        if (!preg_match('/^([a-zA-Z0-9\-_]+)$/', $target)) {
            throw new InvalidArgumentException('Invalid target attribute.');
        }
        $this->target = $target;
        return $this;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function addChild(self $child): self
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * @param array<int,self> $children
     */
    public function addChildren(array $children): self
    {
        foreach ($children as $child) {
            $this->addChild($child);
        }
        return $this;
    }

    public function hasChildren(): bool
    {
        return !empty($this->children);
    }

    /**
     * @return array<int,self>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function setIcon(string $icon): self
    {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setTitleVisible(bool $visible): self
    {
        $this->titleVisible = $visible;
        return $this;
    }

    public function isTitleVisible(): bool
    {
        return $this->titleVisible;
    }

    public function setDisabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;
        return $this;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    public function setVisible(bool $visible): self
    {
        $this->visible = $visible;
        return $this;
    }

    public function isVisible(): bool
    {
        // return false if all children are hidden
        if ($this->visible && $this->hasChildren()) {
            $cnt = 0;
            foreach ($this->children as $child) {
                if ($child->isSeparator()) continue;
                $cnt += $child->isVisible() ? 1 : 0;
            }
            return $cnt > 0;
        }

        return $this->visible;
    }

    /**
     * Iterate items and remove any that are hidden
     * Clean up any separator children removing start/end and duplicates items
     * To be called within the menu build stage
     */
    public function normalize(): static
    {
        if ($this->hasChildren()) {
            $this->children = array_filter($this->children, fn(self $itm) => $itm->isVisible());
        }
        foreach ($this->children as $child) {
            $child->normalize();
        }

        // TODO: normalize separators

        return $this;
    }

    /**
     * Append query params to all item URL's
     * To be called within the menu build stage
     * @param array<string,string> $query
     */
    public function appendQuery(array $query): static
    {
        foreach ($this->children as $child) {
            $child->appendQuery($query);
        }
        // TODO: This should happen at compile time
        //       Only group add queries to local links
        //       An this should live in the top Menu item
        if ($this->showUrl()) {
            $this->setUrl(url()->query($this->getUrl(), $query));
        }
        return $this;
    }

    public function isSeparator(): bool
    {
        return str_starts_with($this->label, self::SEPARATOR);
    }

    public function showUrl(): bool
    {
        return !(empty($this->url) || $this->isDisabled() || $this->isSeparator() || $this->hasChildren());
    }

}
