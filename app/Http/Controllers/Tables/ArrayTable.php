<?php

namespace App\Http\Controllers\Tables;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Records\ArrayRecords;
use Tk\Table\Table;

class ArrayTable extends Controller
{

    public function index(Request $request)
    {
        $this->setPageTitle('Array Table');


        $table1 = $this->buildTable($request);
        $table2 = $this->buildTable($request);

        return view('tables.table-array', Compact('table1', 'table2'));
    }

    protected function buildTable(Request $request): Table
    {

        $rows = [
            ['id' => 1, 'status' => 'active', 'type' => 'docx', 'created' => '2021-01-01'],
            ['id' => 2, 'status' => 'active', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 3, 'status' => 'complete', 'type' => 'bmp', 'created' => '2021-01-01'],
            ['id' => 4, 'status' => 'pending', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 5, 'status' => 'cancelled', 'type' => 'pdf', 'created' => '2021-01-01'],
            ['id' => 6, 'status' => 'active', 'type' => 'pdf', 'created' => '2021-01-01'],
            ['id' => 7, 'status' => 'active', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 8, 'status' => 'complete', 'type' => 'bmp', 'created' => '2021-01-01'],
            ['id' => 9, 'status' => 'pending', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 10, 'status' => 'cancelled', 'type' => 'pdf', 'created' => '2021-01-01'],
            ['id' => 11, 'status' => 'active', 'type' => 'pdf', 'created' => '2021-01-01'],
            ['id' => 12, 'status' => 'active', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 13, 'status' => 'complete', 'type' => 'bmp', 'created' => '2021-01-01'],
            ['id' => 14, 'status' => 'pending', 'type' => 'csv', 'created' => '2021-01-01'],
            ['id' => 15, 'status' => 'cancelled', 'type' => 'pdf', 'created' => '2021-01-01'],
        ];

        $table = new Table();
        $table->setLimit(5);

        $table->appendCell('id')->setSortable();
        $table->appendCell('status')->setSortable();
        $table->appendCell('type')->setSortable();
        $table->appendCell('created')->setSortable();

        $table->setRecords(new ArrayRecords($rows));

        return $table;
    }

}
