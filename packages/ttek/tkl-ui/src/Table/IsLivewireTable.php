<?php

namespace Tk\Table;

use Livewire\Attributes\Url;

/**
 * Use this trait to when adding a table to a Livewire Component class
 *
 * @method void reset(...$properties)
 * @method void resetPage()
 */
trait IsLivewireTable
{
    use IsTable;

    public string $search = '';


    /**
     * Use this method to reset defaults and clear filters
     */
    public function clearFilters(): void
    {
        $this->reset(['search']);
        $this->limit = $this->defaultLimit;
        $this->sort = $this->defaultSort;
        $this->dir = $this->defaultDir;
        $this->resetPage();
    }

    public function queryString(): array
    {
        return [
            'limit' => [
                'except' => config('sis.default.pagination', 30),
                'as' => $this->tableKey('l')
            ],
            'sort' => [
                'except' => $this->defaultSort,
                'as' => $this->tableKey('s')
            ],
            'dir' => [
                'except' => $this->defaultDir,
                'as' => $this->tableKey('d')
            ],
//            'filterVals' => [
//                'except' => [],
//                'as' => $this->tableKey('f')
//            ],
            'search' => [
                'except' => '',
                'as' => $this->tableKey('sr')
            ],
        ];
    }

    public function setLimit(int $limit): void
    {
        if ($this->limit === $limit) return;
        $this->limit = $limit;
        $this->resetPage();
    }

    public function toggleDir(): void
    {
        $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
    }

    public function isLivewire(): bool
    {
        return true;
    }

}
