<?php

namespace Tk\Table\Traits;

use Tk\Support\ItemCollection;
use Tk\Table\Filter;

trait HasFilters
{
    protected ItemCollection $filters;


    /**
     * @return ItemCollection<string, Filter>
     */
    public function getFilters(): ItemCollection
    {
        return $this->filters ??= new ItemCollection();
    }

    /**
     * @return ItemCollection<string, Filter>
     */
    public function getVisibleFilters(): ItemCollection
    {
        return $this->getFilters()->filter(fn(Filter $filter) => $filter->isVisible());
    }

    public function getFilter(string $key): ?Filter
    {
        return $this->getFilters()->get($key);
    }

    public function removeFilter(string $key): static
    {
        $this->getFilters()->forget($key);
        return $this;
    }

    public function appendFilter(string|Filter $filter, ?string $after = null): Filter
    {
        $filter = is_string($filter) ? new Filter($filter) : $filter;
        $filter->setTable($this);

        if ($this->getFilters()->has($filter->getKey())) {
            throw new \Exception("Filter with name '{$filter->getKey()}' already exists.");
        }

        return $this->getFilters()->appendItem($filter->getKey(), $filter, $after);
    }

    public function prependFilter(string|Filter $filter, ?string $before = null): Filter
    {
        $filter = is_string($filter) ? new Filter($filter) : $filter;
        $filter->setTable($this);

        if ($this->getFilters()->has($filter->getKey())) {
            throw new \Exception("Filter with name '{$filter->getKey()}' already exists.");
        }

        return $this->getFilters()->prependItem($filter->getKey(), $filter, $before);
    }


}
