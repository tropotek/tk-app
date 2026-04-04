<?php

use Livewire\Component;
use Tk\Livewire\Table\Table;
use Illuminate\Contracts\Database\Eloquent\Builder as BuilderContract;

new class extends Table {


    public function boot(): void
    {
        $this->limit = 5;

        $this->appendCell('name');
        $this->appendCell('email');
        $this->appendCell('created_at')->setHeader('Created');

        $this->setRecords(new \Tk\Livewire\Table\Records\QueryRecords($this->buildQuery()));
    }

    public function buildQuery(): ?BuilderContract
    {
        // get table filter query
        //$filters = $this->getParams();

        // get idea query builder
        $query = \App\Models\User::query();
        //$query->where('user_id', '=', auth()->id());

        // filter records
        if (!empty($filters['search'])) {
            $q = "LOWER(CONCAT_WS(' ', name, email)) LIKE ?";
            $params = ['%'.strtolower($filters['search']).'%'];
            if (is_numeric($filters['search']) && intval($filters['search']) > 0) {
                $q .= " OR id = ?";
                $params[] = (int) $filters['search'];
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

    public function render()
    {
        return view('tkl-ui::livewire.table.index');
    }
};
?>

<div>
 <p>This is a test</p>
</div>
