<?php

namespace Tk\Table\Records;


use Illuminate\Pagination\AbstractPaginator;
use Tk\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

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
        if ($this->getFilter()) {
            $this->query = call_user_func($this->getFilter(), $this->getQuery());
        }

        // sort results
        $orders = explode(',', $this->getTable()->getOrderBy());
        $orders = array_filter(array_map('trim', $orders));
        foreach ($orders as $order) {
            $dir = 'asc';
            if ($order[0] == '-') {     // descending
                $order = substr($order, 1);
                $dir = 'desc';
            }
            $this->getQuery()->orderBy($order, $dir);
        }

        $this->total = $this->getQuery()->count();

        // paginate results
        $this->getQuery()->forPage(
            $this->getTable()->getPage(),
            $this->getTable()->getLimit()
        );
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

    public function getPaginator(): ?AbstractPaginator
    {
        return \App\Models\Idea::paginate(
            $this->getTable()->getLimit(),
            '[*]',
            $this->getTable()->makeIdKey(\Tk\Table\Table::QUERY_PAGE));
    }
}
