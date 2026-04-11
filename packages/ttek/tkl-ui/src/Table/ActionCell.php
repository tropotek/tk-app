<?php

namespace Tk\Table;

use Illuminate\Database\Eloquent\Model;
use Illuminate\View\ComponentAttributeBag;
use Modules\Core\Table\Cell;

class ActionCell extends Cell
{
    public string $icon = '';

    public function __construct(
        string $name,
        string $icon,
        null|callable $route = null,
        bool $visible = true
    )
    {
        parent::__construct($name);
        $this->icon = $icon;
        $this->setVisible($visible);

        if (is_callable($route)) {
            $this->text = $route;
        }
    }

    public function getHeader(): string
    {
        return sprintf('<i class="%s text-muted" title="%s"></i>', $this->icon, parent::getHeader());
    }

    public function html(mixed $row): string
    {
        if (!$this->isVisible()) return '';
        if (is_callable($this->html)) {
            return call_user_func($this->html, $row, $this);
        }

        return $this->makeActionView($this->text($row), $this->icon, parent::getHeader());
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): ActionCell
    {
        $this->icon = $icon;
        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): ActionCell
    {
        $this->route = $route;
        return $this;
    }


}
