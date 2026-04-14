<?php

namespace Tk\Table\Traits;

use Illuminate\View\ComponentAttributeBag;
use Tk\Support\ItemCollection;

trait HasHeaderAttrs
{
    protected ComponentAttributeBag $headerAttrs;


    public function getHeaderAttrs(): ComponentAttributeBag
    {
        $this->headerAttrs ??= new ComponentAttributeBag();
        return $this->headerAttrs;
    }

    public function addHeaderClass(string $class): static
    {
        $this->headerAttrs = $this->getHeaderAttrs()->class($class);
        return $this;
    }

    public function addHeaderAttrs(array $attrs): static
    {
        $this->headerAttrs = $this->getHeaderAttrs()->merge($attrs);
        return $this;
    }

}
