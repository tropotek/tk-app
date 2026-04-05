<?php

namespace Tk\Tbl;

use Livewire\Attributes\Url;

/**
 * Use this trait to when adding a table to a controller
 */
trait IsLivewireTable
{
    use IsTable;

    #[Url(except: 'tbl')]
    public string $tableId = 'tbl';

    #[Url(except: '30')]
    public int $limit = 30;

    #[Url(except: '')]
    public string $sort = '';

    #[Url(except: 'asc')]
    public string $dir = 'asc';


    /**
     * Livewire method
     */
    public function toggleDir(): void
    {
        // TODO: change to 3 way sort
        $this->dir = $this->dir === 'asc' ? 'desc' : 'asc';
    }


}
