<?php

namespace Tk\Table\Traits;

use Illuminate\Support\Collection;
use Tk\Support\ItemCollection;
use Tk\Table\Column;

trait HasColumns
{
    protected ItemCollection $columns;


    /**
     * @return ItemCollection<string, Column>
     */
    public function getColumns(): ItemCollection
    {
        return $this->columns ??= new ItemCollection();
    }

    /**
     * @return ItemCollection<string, Column>
     */
    public function getVisibleColumns(): ItemCollection
    {
        return $this->getColumns()->filter(fn(Column $column) => $column->isVisible());
    }

    public function getColumn(string $name): ?Column
    {
        return $this->getColumns()->get($name);
    }

    public function removeColumn(string $name): static
    {
        $this->getColumns()->forget($name);
        return $this;
    }

    public function appendColumn(string|Column $column, ?string $after = null): Column
    {
        $column = is_string($column) ? new Column($column) : $column;
        $column->setTable($this);

        if ($this->getColumns()->has($column->getName())) {
            throw new \Exception("Column with name '{$column->getName()}' already exists.");
        }

        return $this->getColumns()->appendItem($column->getName(), $column, $after);
    }

    public function prependColumn(string|Column $column, ?string $before = null): Column
    {
        $column = is_string($column) ? new Column($column) : $column;
        $column->setTable($this);

        if ($this->getColumns()->has($column->getName())) {
            throw new \Exception("Column with name '{$column->getName()}' already exists.");
        }

        return $this->getColumns()->prependItem($column->getName(), $column, $before);
    }


}
