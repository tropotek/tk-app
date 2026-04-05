<?php

namespace Tk\Tbl;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * TODO: This will need more work, but it's a start
 */
class Table
{
    use isTable;

    public LengthAwarePaginator $rows;

    public function setRows(LengthAwarePaginator $rows): static
    {
        $this->rows = $rows;
        return $this;
    }

    public function rows(): LengthAwarePaginator
    {
        return $this->rows();
    }

}
