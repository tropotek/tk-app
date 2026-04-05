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
        Breadcrumbs::push('Example Layout');

        return view('pages.examples.index');
    }

}
