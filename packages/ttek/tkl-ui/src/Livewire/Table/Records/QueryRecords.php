<?php

namespace Tk\Livewire\Table\Records;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Livewire\WithPagination;
use Tk\Livewire\Table\Table;

/**
 * A records class to be used with a query builder.
 */
class QueryRecords extends RecordsInterface
{
    //use WithPagination;

    protected BuilderContract $query;

    public function __construct(BuilderContract $query, int $page = 1)
    {
        $this->query = $query;
        $this->page = $page;
    }

    protected function initQuery(): void
    {
    }

    /**
     * @interface RecordsInterface
     */
    public function toArray(): array
    {
        if (isset($this->records)) return $this->records;

        if (!$this->getTable()) {
            throw new \Exception('Table is not set');
        }

        // filter results using callable
        if ($this->filter) {
            $this->query = call_user_func($this->filter, $this->getTable()->getParams(), $this->getQuery());
        }

        // sort results
//        $orders = explode(',', $this->getTable()->sort);
//        $orders = array_filter(array_map('trim', $orders));
//        foreach ($orders as $order) {
//            $dir = 'asc';
//            if ($order[0] == '-') {     // descending
//                $order = substr($order, 1);
//                $dir = 'desc';
//            }
//            $this->getQuery()->orderBy($order, $dir);
//        }

        // if no direction sort by query default
        if (!empty($this->getTable()->dir)) {
            $this->getQuery()->orderBy($this->getTable()->sort, $this->getTable()->dir);
        }

        $this->total = $this->getQuery()->count();

        // paginate results
        if ($this->getTable()->limit > 0) {
            $this->paginator = $this->getQuery()->paginate(
                $this->getTable()->limit,
                ['*'],
                $this->getTable()->key(Table::QUERY_PAGE)
            );
        }

        $this->records = $this->getQuery()->get()->all();
        return $this->records;
    }

    public function setTable(Table $table): static
    {
        parent::setTable($table);
        return $this;
    }

    public function getQuery(): BuilderContract
    {
        return $this->query;
    }

    public function countAll(): int
    {
        return $this->total;
    }
}
