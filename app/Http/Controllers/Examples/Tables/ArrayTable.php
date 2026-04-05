<?php

namespace App\Http\Controllers\Examples\Tables;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Tbl\Cell;
use Tk\Tbl\IsTable;

class ArrayTable extends Controller
{
    use IsTable;

    protected LengthAwarePaginator $rows;

    public function index(Request $request)
    {
        Breadcrumbs::push('Array Table');

        $this->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'roles',
            sortable: false,
//            text: function ($row) {
//                return $row->roles->pluck('name')->implode(', ');
//            },
        ));

        // alt method to add cells
        $this->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
//            ->setText(function ($row) {
//                return $row->created_at->format('Y-m-d h:i');
//            })
        ;

        $this->hydrateTableFromRequest();

        return view('pages.examples.tables.table-array', [
            'table' => $this,
        ]);
    }

    public function rows(): LengthAwarePaginator
    {
        if (isset($this->rows)) return $this->rows;

        $rows = [
            (object)['id' => 1, 'name' => 'Test 1', 'email' => 'email1@example.com', 'roles' => 'test', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test 2', 'email' => 'email2@example.com', 'roles' => 'test', 'created_at' => '2021-01-02 12:23:33'],
            (object)['id' => 3, 'name' => 'Test 3', 'email' => 'email3@example.com', 'roles' => 'test', 'created_at' => '2021-03-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test 4', 'email' => 'email4@example.com', 'roles' => 'test', 'created_at' => '2021-07-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'test', 'created_at' => '2021-04-25 12:23:33'],
            (object)['id' => 6, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'test', 'created_at' => '2021-02-03 12:23:33'],
            (object)['id' => 7, 'name' => 'Test 9', 'email' => 'email9@example.com', 'roles' => 'test', 'created_at' => '2021-02-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test 19', 'email' => 'email10@example.com', 'roles' => 'test', 'created_at' => '2021-04-12 12:23:33'],
        ];

        // 1. filter results with any filters if available


        // 2. sort results (todo: using Gregs sort method for now, review)
        $sortCol = ($this->tableDir == 'desc' ? '-' : '') . $this->safeSort();
        $rows = $this->sortRows($rows, $sortCol);

        // 3. return/cache paginated results
        $this->rows = $this->paginateArray($rows);
        return $this->rows;
    }

}
