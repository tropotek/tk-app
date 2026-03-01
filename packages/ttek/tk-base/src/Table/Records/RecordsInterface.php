<?php

namespace Tk\Table\Records;

use Illuminate\Pagination\AbstractPaginator;
use Tk\Table\Table;

/**
 *
 * @todo make this iterable with paging etc.
 *
 */
abstract class RecordsInterface
{

    protected Table $table;
    protected array $records;
    protected mixed $filter = null;
    protected int $total = 0;

    /**
     * This method should write the filtered, sorted, and pagenated rows to the
     * $this->records array when not set, then return the records array
     *
     */
    abstract public function toArray(): array;

    public function count(): int
    {
        return count($this->toArray());
    }

    public function countAll(): int
    {
        return $this->total;
    }

    public function getTable(): Table
    {
        return $this->table;
    }

    public function setTable(Table $table): static
    {
        $this->table = $table;
        $this->table->refreshState();
        return $this;
    }

    public function getFilter(): ?callable
    {
        return $this->filter;
    }

    public function setFilter(callable $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    public function getPaginator(): ?AbstractPaginator
    {
        return null;
    }

}
