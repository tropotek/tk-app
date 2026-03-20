<?php

namespace App\Livewire\Tables;

use App\Models\User;
use Illuminate\Contracts\Database\Query\Builder;
use Tk\Livewire\Table\FilterTable;
use Tk\Livewire\Table\Records\QueryRecords;
use Tk\Livewire\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

class LivewireTable extends Table
{

    public function build(): void
    {
        $this->appendCell('name');
        $this->appendCell('email');
        $this->appendCell('created_at')->setHeader('Created');

        $this->setRecords(new QueryRecords($this->buildQuery()));

    }

    public function buildQuery(): ?BuilderContract
    {
        // get table filter query
        //$filters = $this->getParams();

        // get idea query builder
        $query = User::query();
        //$query->where('user_id', '=', auth()->id());

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

        if (!empty($filters['name'])) {
            $query->where('name', '=', $filters['name']);
        }

        if (!empty($filters['email'])) {
            $query->where('email', '=', $filters['email']);
        }

        //vd($query->toRawSql());
        return $query;
    }

}
