<?php

namespace Tk\Table\Records;

use Tk\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

class ArrayRecords extends RecordsInterface
{
    protected array $rows;

    public function __construct(Table $table, array $rows)
    {
        parent::__construct($table);
        $this->rows = $rows;
    }

    public function getRecords(): array
    {
        // sort results

        // pagenate results

        // return rows as an array
        return $this->rows;
    }

    public function getRows(): array
    {
        return $this->rows;
    }
}
