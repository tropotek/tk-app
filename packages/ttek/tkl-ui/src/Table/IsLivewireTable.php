<?php

namespace Tk\Table;

/**
 * Use this trait to add a table to a Livewire Component
 *
 * Livewire Component methods:
 * @method void reset(...$properties)
 * @method void resetPage(string $pageName = 'page')
 * @uses IsSearchable
 * @property string $search
 * @property string $searchPlaceholder
 * @property array $searchClear
 */
trait IsLivewireTable
{
    use IsTable;

    /**
     * provides alias to the query vals and hides the default value params
     */
    public function queryString(): array
    {
        // set the initial default limit value, called by the Livewire system
        $this->setDefaultLimit(config('sis.default.pagination', 30));

        $qs = [
            'limit' => [
                'except' => 0,
                'as' => $this->tableKey(self::QUERY_LIMIT)
            ],
            'sort' => [
                'except' => '',
                'as' => $this->tableKey(self::QUERY_SORT)
            ],
            'dir' => [
                'except' => '',
                'as' => $this->tableKey(self::QUERY_DIR)
            ],
            'filterVals' => [
                'except' => [],
                'as' => $this->tableKey(self::QUERY_FILTER)
            ],
        ];

        if ($this->isSearchable()) {
            $qs['search'] = [
                'except' => '',
                'as' => $this->tableKey(self::QUERY_SEARCH)
            ];
        }

        return $qs;
    }

    /**
     * Use this method to reset defaults and clear filters
     */
    public function clearFilters(): void
    {
        $reset = ['filterVals'];
        if ($this->isSearchable()) {
            $reset[] = 'search';
        }
        $this->reset($reset);

        $this->limit = 0;
        $this->sort = '';
        $this->dir = '';

        foreach ($this->getFilters() as $filter) {
            if ($filter->getDefaultValue()) {
                $this->filterVals[$filter->key] = $filter->getDefaultValue();
            }
        }

        $this->resetPage($this->tableKey(self::QUERY_PAGE));
    }

    /**
     * update filter dependencies
     */
    public function updateFilters(string $filterKey): void
    {
        foreach ($this->getVisibleFilters() as $filter) {
            if ($filter->getDependsOn() === $filterKey) {
                unset($this->filterVals[$filter->getKey()]);
            }
        }

        $this->resetPage($this->tableKey(self::QUERY_PAGE));
    }
    public function updatedSearch(): void
    {
        if ($this->isSearchable()) {
            foreach ($this->searchClear as $filterKey) {
                unset($this->filterVals[$filterKey]);
            }
        }

        $this->resetPage($this->tableKey(self::QUERY_PAGE));
    }

    public function toggleDir(): void
    {
        $this->dir = $this->getDir() === self::SORT_DESC ? '' : self::SORT_DESC;
    }

    public function setSort(string $key): void
    {
        if (!in_array($key, $this->sortableKeys())) return;

        if ($this->getSort() === $key) {
            $this->dir = $this->getDir() === self::SORT_DESC ? '' : self::SORT_DESC;
        } else {
            $this->sort = $key;
            $this->dir  = '';
        }

        $this->resetPage($this->tableKey(self::QUERY_PAGE));
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
        $this->resetPage($this->tableKey(self::QUERY_PAGE));
    }

    public function isLivewire(): bool
    {
        return true;
    }

}
