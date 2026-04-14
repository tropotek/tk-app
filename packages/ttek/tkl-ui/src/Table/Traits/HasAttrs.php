<?php

namespace Tk\Table\Traits;

use Illuminate\View\ComponentAttributeBag;
use Tk\Support\ItemCollection;

trait HasAttrs
{
    protected ComponentAttributeBag $attrs;


    public function getAttrs(): ComponentAttributeBag
    {
        $this->attrs ??= new ComponentAttributeBag();
        return $this->attrs;
    }

    public function addClass(string $class): static
    {
        $this->attrs = $this->getAttrs()->class($class);
        return $this;
    }

    public function addAttrs(array $attrs): static
    {
        $this->attrs = $this->getAttrs()->merge($attrs);
        return $this;
    }

}
