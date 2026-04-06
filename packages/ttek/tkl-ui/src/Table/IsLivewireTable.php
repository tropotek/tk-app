<?php

namespace Tk\Table;

use Livewire\Attributes\Url;

/**
 * Use this trait to when adding a table to a Livewire Component class
 *
 * @method void reset()
 * @method void resetPage()
 */
trait IsLivewireTable
{
    use IsTable;

    #[Url(except: 'tbl')]
    public string $tableId = 'tbl';

    #[Url(except: 30)]
    public int $limit = 30;

    #[Url(except: '')]
    public string $sort = '';

    #[Url(except: 'asc')]
    public string $dir = 'asc';

    #[Url(except: '')]
    public string $search = '';


    /**
     * Use this method to reset defaults and clear filters
     */
    public function clearFilters(): void
    {
        $this->reset();
        $this->limit = $this->defaultLimit;
        $this->sort = $this->defaultSort;
        $this->dir = $this->defaultDir;
        $this->resetPage();
    }

    public function setLimit(int $limit): void
    {
        if ($this->limit === $limit) return;
        $this->limit = $limit;
        $this->resetPage();
    }

    /**
     * Livewire method
     */
    public function toggleDir(): void
    {
        // TODO: change to 3 way sort
        $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
    }

}
