<?php

namespace App\Http\Controllers\Examples;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Cell;
use Tk\Table\IsTable;

/**
 * This is an example of using the Table trait within a controller.
 * Data is sourced from an array.
 *
 */
class ArrayTableController extends Controller
{
    use IsTable;

    protected LengthAwarePaginator $rows;

    public function index(Request $request)
    {
        Breadcrumbs::push('Table Array');

        $this->appendCell(new Cell(
            name: 'name',
            sortable: true,
        ))->addClass('fw-bold');

        $this->appendCell(new Cell(
            name: 'email',
            sortable: true,
        ));

        $this->appendCell(new Cell(
            name: 'roles',
            sortable: false,
        ));

        // alt method to add cells
        $this->appendCell(new Cell('created_at'), 'roles')
            ->setHeader('Created')
            ->setSortable()
        ;

        $this->hydrateTableFromRequest();
        if (request()->has($this->tableKey('reset'))) {
            // remove all table query params
            return redirect(url()->current());
        }

        return view('pages.examples.tables.table-array', [
            'table' => $this,
        ]);
    }

    public function rows(): LengthAwarePaginator
    {
        if (isset($this->rows)) return $this->rows;

        $rows = [
            (object)['id' => 1, 'name' => 'Test 1', 'email' => 'email1@example.com', 'roles' => 'admin', 'created_at' => '2021-01-01 12:23:33'],
            (object)['id' => 2, 'name' => 'Test 2', 'email' => 'email2@example.com', 'roles' => 'staff', 'created_at' => '2021-01-02 12:23:33'],
            (object)['id' => 3, 'name' => 'Test 3', 'email' => 'email3@example.com', 'roles' => 'staff', 'created_at' => '2021-03-01 12:23:33'],
            (object)['id' => 4, 'name' => 'Test 4', 'email' => 'email4@example.com', 'roles' => 'admin,staff', 'created_at' => '2021-07-01 12:23:33'],
            (object)['id' => 5, 'name' => 'Test 6', 'email' => 'email6@example.com', 'roles' => 'member', 'created_at' => '2021-04-25 12:23:33'],
            (object)['id' => 6, 'name' => 'Test 8', 'email' => 'email8@example.com', 'roles' => 'member', 'created_at' => '2021-02-03 12:23:33'],
            (object)['id' => 7, 'name' => 'Test 9', 'email' => 'email9@example.com', 'roles' => 'admin,staff', 'created_at' => '2021-02-01 12:23:33'],
            (object)['id' => 8, 'name' => 'Test 19', 'email' => 'email10@example.com', 'roles' => 'member', 'created_at' => '2021-04-12 12:23:33'],
        ];

        // 1. filter results with any filters if available
        // search
        $search = request()->input($this->tableKey('search'), '');
        if ($search) {
            $rows = array_filter($rows, function ($row) use ($search) {
                return str_contains(strtolower($row->name), strtolower($search));
            });
        }

        // 2. sort results (todo: using Gregs sort method for now, review)
        $sortCol = ($this->dir == 'desc' ? '-' : '') . $this->safeSort();
        $rows = $this->sortArray($rows, $sortCol);

        // 3. return/cache paginated results
        $this->rows = $this->paginateArray($rows);
        return $this->rows;
    }

}
