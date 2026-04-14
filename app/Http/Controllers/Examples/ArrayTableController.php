<?php

namespace App\Http\Controllers\Examples;


use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Cell;
use Tk\Table\IsSearchable;
use Tk\Table\IsTable;

/**
 * This is an example of using the Table trait within a controller.
 * Data is sourced from an array.
 *
 */
class ArrayTableController extends Controller
{
    use IsTable, IsSearchable;

    public function __construct()
    {

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

    }

    public function index(Request $request)
    {
        Breadcrumbs::push('Table Array');

        $this->hydrateTableFromRequest();

        if (request()->has($this->tableKey('reset'))) {
            // remove all table query params
            return redirect(url()->current());
        }

        return view('pages.examples.tables.table-array', [
            'table' => $this,
        ]);
    }

    public function rows(): array|Builder
    {

        $rows = session()->get('_side-table-cache');

        if (!$rows) {
            $faker = \Faker\Factory::create();
            $rows = array_map(fn($i) => [
                'id' => $i,
                'name' => $faker->company(),
                'email' => $faker->email(),
                'roles' => $faker->randomElement(['admin', 'staff', 'member']),
                'created_at' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d H:i:s'),
            ], range(1, 50));
            session()->put('_side-table-cache', $rows);
        }

        // 1. filter results with any filters if available
        // search
        $search = request()->input($this->tableKey('search'), '');
        if ($search) {
            $rows = array_filter($rows, function ($row) use ($search) {
                return str_contains(strtolower($row->name), strtolower($search));
            });
        }

        return $rows;
    }

    public function export(): StreamedResponse
    {
        $this->hydrateTableFromRequest();
        return $this->exportCsv($this->rows(), 'demo-controller-table.csv');
    }

}
