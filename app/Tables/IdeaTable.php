<?php

namespace App\Tables;

use App\Models\Idea;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;
use Illuminate\Support\Facades\Request;
use Tk\Table\Cell;
use Tk\Table\Records\QueryRecords;
use Tk\Table\Table;

class IdeaTable extends Table
{

    protected function build(): void
    {

        $this->addRowAttrs(function (Idea $row, Table $table) {
            return ['data-test-id' => $row->id];
        });

        $this->appendCell('title')
            ->setSortable()
            //->addClass('max-width')  // TODO might stop using this method, let the table resize organically
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

        $filters = Request::validate([
            'search' => 'string',
        ]);

        $this->setRecords(new QueryRecords($this->buildQuery($filters)));
    }

    public function buildQuery(array $filters = []): ?BuilderContract
    {
        $query = Idea::query();

        // filter records
        if (!empty($filter['search'])) {
            $filter['lSearch'] = '%' . strtolower($filter['search']) . '%';
            $w  = "user_id = :search ";
            $w .= "OR LOWER(CONCAT_WS(' ', title, description)) LIKE :lSearch ";
            $query->whereRaw($w, $filter);
        }

        if (!empty($filters['title'])) {
            $query->where('title', '=', $filters['title']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }

        return $query;
    }

}
