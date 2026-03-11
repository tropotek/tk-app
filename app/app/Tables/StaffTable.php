<?php

namespace App\Tables;

use App\Models\Idea;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Tk\Table\Cell;
use Tk\Table\Records\QueryRecords;
use Tk\Table\Table;

class StaffTable extends Table
{

    protected function build(): void
    {
        $this->appendCell('row_id')
            ->setComponent(Cell::COMP_ROW_SELECT)
            ->setValue(function (Staff $row , Cell $cell) {
                return $row->id;
            });

        $this->appendCell('name')
            ->setSortable()
            ->setHtml(function (Staff $row , Cell $cell) {
                return "<a href='/user/{$row->id}'>{$cell->getValue($row)}</a>";
            });

        $this->appendCell('email')
            ->setSortable();

        $this->appendCell('role')
            ->setSortable()
            ->setValue(function (Staff $row , Cell $cell) {
                return $row->roles->implode('name', ', ');
            });

        $this->appendCell('created_at')
            ->setHeader('Created')
            ->addClass('text-nowrap')
            ->setSortable()
            ->addAttr(['data-test-string' => 'someId'])
            ->setValue(function ($row, Cell $cell) {
                $cell->addAttr(['data-test-id' => $row->id]);
                return $row->created_at->format('Y-m-d h:i');
            });

        // Get the table rows
        $this->setRecords(new QueryRecords($this->buildQuery()));
    }

    public function buildQuery(): ?BuilderContract
    {
        // get table filter query
        $filters = $this->getParams();

        // get idea query builder
        $query = Staff::query();

        // filter records
        if (!empty($filters['search'])) {
            $q = "LOWER(CONCAT_WS(' ', name, email)) LIKE ?";
            $params = ['%' . strtolower($filters['search']) . '%'];
            if (is_numeric($filters['search']) && intval($filters['search']) > 0) {
                $q .= " OR id = ?";
                $params[] = (int)$filters['search'];
            }
            $query->whereRaw("($q)", $params);
        }

        if (!empty($filters['email'])) {
            $query->where('email', '=', $filters['email']);
        }

        //vd($query->toRawSql());
        return $query;
    }

}
