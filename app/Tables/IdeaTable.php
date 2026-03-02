<?php

namespace App\Tables;

use App\Models\Idea;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Tk\Table\Cell;
use Tk\Table\Records\QueryRecords;
use Tk\Table\Table;

class IdeaTable extends Table
{

    protected function build(): void
    {
        $this->addClass('testing');
        $this->addAttr(['data-test' => 'testing']);

        $this->setLimit(3);

        $this->addRowAttrs(function (Idea $row, Table $table) {
            return ['data-test-id' => $row->id];
        });


        $this->appendCell('id')
            ->setComponent(Cell::COMP_ROW_SELECT);

        $this->appendCell('title')
            ->setSortable()
            ->setHtml(function ($row , Cell $cell) {
                return "<a href='/ideas/{$row->id}/edit'>{$cell->getValue($row)}</a>";
            });

        $this->appendCell('status')
            ->addClass('text-nowrap')
            ->setSortable()
            ->setValue(function ($row, Cell $cell) {
                return $row->status->label();
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

        $this->appendCell('updated_at')
            ->setHeader('Modified')
            ->addClass('text-nowrap')
            ->setSortable()
            ->setValue(function ($row, Cell $cell) {
                return $row->updated_at->format('Y-m-d h:i');
            });

        $this->setRecords(new QueryRecords($this->buildQuery()));
    }

    public function buildQuery(): ?BuilderContract
    {
        // get table filter query
        $filters = $this->getStateList();

        // get idea query builder
        $query = Idea::query();

        // filter records
        if (!empty($filters['search'])) {
            $q = "LOWER(CONCAT_WS(' ', title, description)) LIKE ?";
            $params = ['%' . strtolower($filters['search']) . '%'];
            if (is_numeric($filters['search']) && intval($filters['search']) > 0) {
                $q .= " OR id = ?";
                $params[] = (int)$filters['search'];
            }
            $query->whereRaw("($q)", $params);
        }

        if (!empty($filters['title'])) {
            $query->where('title', '=', $filters['title']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        //vd($query->toRawSql());
        return $query;
    }

}
