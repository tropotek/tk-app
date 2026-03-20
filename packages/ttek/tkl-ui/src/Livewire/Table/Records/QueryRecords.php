<?php

namespace Tk\Livewire\Table\Records;

use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Tk\Livewire\Table\Table;

/**
 * A records class to be used with a query builder.
 */
class QueryRecords extends RecordsInterface
{
    protected BuilderContract $query;

    public function __construct(BuilderContract $query)
    {
        $this->query = $query;
    }

    protected function initQuery(): void
    {
        // filter results using callable
//        if ($this->filter) {
//            $this->query = call_user_func($this->filter, $this->getTable()->getParams(), $this->getQuery());
//        }

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
            $this->table->getPaginator();

            $this->paginator = $this->getQuery()->paginate(
                $this->getTable()->limit,
                '[*]',
                $this->getTable()->key(\Tk\Table\Table::QUERY_PAGE),
                $this->getTable()->page
            )->withQueryString(); //->appends(Table::QUERY_ID, $this->getTable()->getId());
        }
    }

    public function setTable(Table $table): static
    {
        parent::setTable($table);
        $this->initQuery();
        return $this;
    }

    public function getQuery(): BuilderContract
    {
        return $this->query;
    }

    /**
     * @interface RecordsInterface
     */
    public function toArray(): array
    {
        if (!isset($this->records)) {
            $this->records = $this->getQuery()->get()->all();
        }
        return $this->records;
    }

    public function countAll(): int
    {
        return $this->total;
    }
}
