<?php

namespace Tk\Table\Traits;

use Illuminate\View\ComponentAttributeBag;

trait HasAttributes
{
    protected ComponentAttributeBag $_attributes;


    public function getAttributes(): ComponentAttributeBag
    {
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

    /**
     * @todo allow callables
     * @callable function (mixed $row, Table $table):array { return ['class' => 'test']; }
     */
    public function addAttr(array $attrs): static
    {
        $this->_attributes = $this->_attributes->merge($attrs);
        return $this;
    }
}
