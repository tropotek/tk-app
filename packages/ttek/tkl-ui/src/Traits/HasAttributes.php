<?php

namespace Tk\Traits;


use Illuminate\View\ComponentAttributeBag;

/**
 * @todo: rename this as to not confuse it with Model attributes
 */
trait HasAttributes
{
    protected ComponentAttributeBag $_attributes;


    public function getAttrs(): ComponentAttributeBag
    {
        if (empty($this->_attributes)) {
            $this->_attributes = new ComponentAttributeBag();
        }
        return $this->_attributes;
    }

    /**
     * Add a CSS class to the cell
     */
    public function addClass(string $class): static
    {
        $this->_attributes = $this->_attributes->class($class);
        return $this;
    }

    public function addAttr(array $attrs): static
    {
        $this->_attributes = $this->_attributes->merge($attrs);
        return $this;
    }
}
