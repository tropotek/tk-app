<?php

namespace Tk\Table;

class ActionColumn extends Column
{
    public string $icon = '';

    public function __construct(
        string $name,
        string $icon,
        null|callable $route = null,
        null|callable $view = null,
        bool $visible = true
    )
    {
        parent::__construct($name);
        $this->icon = $icon;
        $this->setVisible($visible);

        if (is_callable($route)) $this->value = $route;
        if (is_callable($view)) $this->view = $view;
    }

    public function getHeader(): string
    {
        return sprintf('<i class="%s text-muted" title="%s"></i>',
            $this->icon,
            e(parent::getHeader())
        );
    }

    public function view(mixed $row): mixed
    {
        if (!$this->isVisible()) return '';
        if (is_callable($this->view)) {
            $ret = call_user_func($this->view, $row, $this);
            if (is_string($ret) || $ret instanceof \Stringable) {
                return $ret;
            }
        }

        return view('core::components.table.columns.a', [
            'href' => $this->value($row),
            'icon' => $this->icon
        ]);
    }

    public function getIcon(): string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): static
    {
        $this->icon = $icon;
        return $this;
    }
}
