<?php

namespace App\Http\Controllers\Tables;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Records\CsvRecords;
use Tk\Table\Table;

class CsvTable extends Controller
{

    public function index(Request $request)
    {
        $this->setPageTitle('Csv File Table');

        $table = $this->buildCsvTable($request);

        return view('tables.table-csv', Compact('table'));
    }

    protected function buildCsvTable(Request $request): Table
    {
        $table = new Table();
        $table->setLimit(5);
        $table->setOrderBy('-id');

        $table->appendCell('id')->setSortable();
        $table->appendCell('Status')->setSortable();
        $table->appendCell('Type')->setSortable();
        $table->appendCell('Created')->setSortable();

        $records = new CsvRecords(app_path('../public/assets/test.csv'));

        // Setup array filters for records
        $records->filter(function ($filters, $rows) {
            $s = $filters['search'] ?? '';
            if ($s) {
                $s = strtolower(preg_replace("/[^a-zA-Z0-9@'\. -]/", ' ', $s));
                foreach (array_filter(explode(' ', $s)) as $term) {
                    $rows = array_filter($rows, fn($r) => str_contains(
                        strtolower("{$r['Status']} {$r['Type']}"), $term)
                    );
                }
            }

            $s = $filters['status'] ?? '';
            $rows = match($s) {
                '', 'all' => $rows,
                default   => array_filter($rows, fn($r) => $r['Status'] == $s),
            };

            $s = $filters['type'] ?? '';
            $rows = match($s) {
                '', 'all' => $rows,
                default   => array_filter($rows, fn($r) => $r['Type'] == $s),
            };

            return $rows;
        });

        $table->setRecords($records);

        return $table;
    }

}
