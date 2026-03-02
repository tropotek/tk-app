<?php

namespace Tk\Table\Records;

use Illuminate\Pagination\AbstractPaginator;
use Tk\Table\Table;

/**
 *
 */
abstract class RecordsInterface implements \IteratorAggregate, \Countable
{

    protected Table $table;
    protected array $records;
    protected mixed $filter = null;
    protected int $total = 0;

    /**
     * This method should write the filtered, sorted, and paginated rows to the
     * $this->records array when not set, then return the records array
     */
    abstract public function toArray(): array;

    /**
     * @interface \IteratorAggregate
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * @interface \Countable
     */
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
        // refresh the table orderBy, limit and page values
        // required so Result objects have access to the current table state
        $this->table->refreshParams();
        return $this;
    }

    /**
     * @callable function(array $filters, array|query $rows): array;
     */
    public function filter(callable $filter): static
    {
        $this->filter = $filter;
        return $this;
    }

    public function getPaginator(): ?AbstractPaginator
    {
        return null;
    }

}
