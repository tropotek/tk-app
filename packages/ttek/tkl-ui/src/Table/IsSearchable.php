<?php

namespace Tk\Table;

trait IsSearchable
{
    public ?string $search = '';

    public string $searchPlaceholder = 'Search...';

    /**
     * A list of filters to clear on search
     */
    public array $searchClear = [];
}
