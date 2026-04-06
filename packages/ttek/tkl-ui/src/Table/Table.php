<?php

namespace Tk\Table;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * A stand-alone table object that can be rendered using the
 * component templates:
 *  - `/components/table/index.blade.php`
 *  - `/components/table/livewire/index.blade.php`
 *
 *
 */
class Table
{
    use isTable;

    public LengthAwarePaginator $rows;

    public function __construct(string $tableId = 'tbl')
    {
        $this->tableId = $tableId;
    }

    public function setRows(LengthAwarePaginator $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function rows(): LengthAwarePaginator
    {
        if (empty($this->rows)){
            throw new \Exception("Use setRows() before rendering the table");
        }
        return $this->rows();
    }

}
