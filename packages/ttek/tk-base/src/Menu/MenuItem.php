<?php
namespace Tk\Menu;

use InvalidArgumentException;

/**
 * Use the MenuItem class to build a menu/nav structure.
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
final class MenuItem
{
    private string  $label;
    private string  $url          = '/';
    private string  $icon         = '';
    private string  $target       = '_self';
    /** @var bool | callable */
    private         $visible      = true;
    /** @var bool | callable */
    private         $disabled     = false;
    private bool    $titleVisible = true;
    private array   $children     = [];


    public static function make(string $label, string $url = ''): self
    {
        $item = new self();
        $item->setLabel($label);
        if ($url) {
            $item->setUrl($url);
        }
        return $item;
    }

    public static function makeSeparator(): self
    {
        static $id = 0;
        return self::make('---' . $id++);
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

    public function setUrl(string $url): self
    {
        $this->url = $url;
        return $this;
    }

    public function getUrl(): string
    {
        if (!$this->showUrl()) return '';
        return $this->url;
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

    public function setDisabled($disabled, bool $includeChildren = true): self
    {
        if (!is_bool($disabled) && !is_callable($disabled)) {
            throw new InvalidArgumentException('The property must be a boolean or a callable.');
        }

        $this->disabled = $disabled;

        if ($includeChildren) {
            foreach ($this->children as $child) {
                $child->setDisabled($disabled);
            }
        }

        return $this;
    }

    public function isDisabled(): bool
    {

        return is_callable($this->disabled) ? call_user_func($this->disabled) : $this->disabled;
    }

    public function setVisible($visible): self
    {
        if (!is_bool($visible) && !is_callable($visible)) {
            throw new InvalidArgumentException('The property must be a boolean or a callable.');
        }

        $this->visible = $visible;

        return $this;
    }

    public function isVisible(): bool
    {
        return is_callable($this->visible) ? call_user_func($this->visible) : $this->visible;
    }

    public function isSeparator(): bool
    {
        return str_starts_with($this->label, '---');
    }

    public function showUrl(): bool
    {
        return !($this->isDisabled() || $this->isSeparator() || $this->hasChildren());
    }

}
