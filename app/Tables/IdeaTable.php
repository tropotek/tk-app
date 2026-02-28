<?php

namespace App\Tables;

use App\Models\Idea;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Tk\Table\Cell;
use Tk\Table\Table;

class IdeaTable extends Table
{

    protected function build(): void
    {

        $this->appendCell('title')
            ->setHtml(function ($row , Cell $cell) {
                return "<a href='/ideas/{$row->id}/edit'>{$cell->getValue($row)}</a>";
            });

        $this->appendCell('status')
            ->setValue(function ($row, Cell $cell) {
                return $row->status->label();
            });

        $this->appendCell('created_at')
            ->setHeader('Created')
            ->setValue(function ($row, Cell $cell) {
                return $row->created_at->format('d-m-Y');
            });

        // get the filtered rows using the request
        //$this->getRows(request()->all());

    }


    public function query(array $filters = []): ?BuilderContract
    {
        $query = Idea::query();

        // pagination and sorting
        if (!empty($orderby)) $query->orderBy($this->getOrderBySql());
        $query->forPage($this->getPage(), $this->getLimit());

        if (!empty($filter['search'])) {
            $filter['lSearch'] = '%' . strtolower($filter['search']) . '%';
            $w  = "user_id = :search ";
            $w .= "OR LOWER(CONCAT_WS(' ', title, description)) LIKE :lSearch ";
            $query->whereRaw($w, $filter);
            //$filter->appendWhere('AND (%s)', $w);
        }

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }
vd($query->toSql());
        return $query;
    }

}
