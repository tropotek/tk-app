<?php

namespace App\Http\Controllers\Tables;


use App\Http\Controllers\Controller;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tk\Support\Facades\Breadcrumbs;
use Tk\Table\Table;

class QueryTable extends Controller
{

    public function index(Request $request)
    {
        $this->setPageTitle('SQL Query Table');

        $table = new IdeaTable();

        return view('tables.table-example', Compact('table'));
    }

}
