<?php

namespace Tk\Table\Traits;

trait WithSearch
{
    public ?string $search = '';

    public bool $searchEnabled = true;

    public string $searchPlaceholder = 'Search...';

    /**
     * A list of filters to clear on search
     */
    public array $searchClear = [];


    public function enableSearch(): static
    {
        $this->searchEnabled = true;
        return $this;
    }
    public function disableSearch(): static
    {
        $this->searchEnabled = false;
        return $this;
    }

    public function searchable(): bool
    {
        return $this->searchEnabled;
    }
}
