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

        // example of using the action params
        if ($request->has('tbl_delete')) {
            vd($table->getParams(), $request->all());

            // perform required action (delete, csv, etc...)

            // reset the url removing the action params
            $url = $table->resetUrl([
                'tbl_delete' => null,
                'row_id' => null,
                'row_id_all' => null,
            ]);

            return redirect($url)->with('success', "Table Action Completed.");
        }

        return view('tables.table-query', Compact('table'));
    }

}
