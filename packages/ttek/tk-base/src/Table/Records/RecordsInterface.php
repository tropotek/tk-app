<?php

namespace Tk\Table\Records;

use Tk\Table\Table;

abstract class RecordsInterface
{

    protected Table $table;

    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    abstract public function getRecords(): array;


    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): void
    {
        $this->table = $table;
    }
}
