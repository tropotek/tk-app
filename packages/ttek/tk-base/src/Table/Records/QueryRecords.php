<?php

namespace Tk\Table\Records;

use Tk\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

class QueryRecords extends RecordsInterface
{
    protected BuilderContract $query;

    public function __construct(Table $table, BuilderContract $query)
    {
        parent::__construct($table);
        $this->query = $query;
    }

    public function getRecords(): array
    {
        // sort results
        $orders = explode(',', $this->getTable()->getOrderBy());
        $orders = array_filter(array_map('trim', $orders));
        foreach ($orders as $order) {
            $dir = 'asc';
            if ($order[0] == '-') {     // descending
                $order = substr($order, 1);
                $dir = 'desc';
            }
            $this->getTable()->getQuery()->orderBy($order, $dir);
        }

        // pagenate results
        $this->getTable()->getQuery()->forPage(
            $this->getTable()->getPage(),
            $this->getTable()->getLimit()
        );

        // return rows as an array
        return $this->query->get()->all();
    }

    public function getQuery(): BuilderContract
    {
        return $this->query;
    }

}
