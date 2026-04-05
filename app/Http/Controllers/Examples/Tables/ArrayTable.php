<?php

namespace App\Http\Controllers\Examples\Tables;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Tbl\Cell;
use Tk\Tbl\HasTable;
use Tk\Tbl\Table;

class ArrayTable extends Controller
{
    use HasTable;

    public function index(Request $request)
    {
        Breadcrumbs::push('Array Table');

        $this->table = new Table();

        $this->table->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ));

        $this->table->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->table->appendCell(new Cell(
            name: 'roles',
            sortable: false,
//            text: function ($row) {
//                return $row->roles->pluck('name')->implode(', ');
//            },
        ));

        // alt method to add cells
        $this->table->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
//            ->setText(function ($row) {
//                return $row->created_at->format('Y-m-d h:i');
//            })
        ;



        return view('pages.examples.tables.table-array', [
            'table' => $this->table,
            'rows' => $this->rows(),
        ]);
    }

    public function rows(): \Traversable
    {

        $rows = [
            (object)['id' => 1, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 3, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 6, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 7, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test', 'email' => 'email@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
        ];

        // 1. filter results with any filters if available


        // 2. sort results (todo: using Gregs sort method for now, review)
        $sortCol = ($this->dir == 'desc' ? '-' : '') . $this->safeSort();
        $rows = $this->sortRows($rows, $sortCol);

        // 3. return paginated results
        return $this->paginate($rows);
    }

}
