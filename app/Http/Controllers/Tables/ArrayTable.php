<?php

namespace App\Http\Controllers\Tables;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Table;

class ArrayTable extends Controller
{

    public function index(Request $request)
    {
        $this->setPageTitle('Array Table');


        $table = new Table();

        return view('tables.table-example', Compact('table'));
    }

}
