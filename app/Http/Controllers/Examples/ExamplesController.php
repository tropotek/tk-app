<?php

namespace App\Http\Controllers\Examples;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Records\CsvRecords;
use Tk\Table\Table;

class ExamplesController extends Controller
{

    public function index(Request $request)
    {
        Breadcrumbs::push('Examples');

        $table = $this->buildTable($request);

        return view('pages.examples.examples', [
            'table' => $table
        ]);
    }

    protected function buildTable(Request $request): Table
    {
        $table = new Table();
        $table->setLimit(5);
        $table->setOrderBy('-id');

        $table->appendCell('id')->setSortable();
        $table->appendCell('Status')->setSortable();
        $table->appendCell('Type')->setSortable();
        $table->appendCell('Created')->setSortable();

        $table->setRecords(new CsvRecords(app_path('../public/assets/test.csv')));

        return $table;
    }

}
