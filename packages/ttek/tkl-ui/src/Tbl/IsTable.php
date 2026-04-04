<?php

namespace Tk\Tbl;
use Livewire\Attributes\Url;

/**
 * Use this trait to make a Livewire component a table.
 */
trait IsTable
{
    use HasTable;

    #[Url(except: 30)]
    public int $limit = 30;

    #[Url(except: '')]
    public string $sort = '';

    #[Url(except: 'asc')]
    public string $dir = 'asc';

}
