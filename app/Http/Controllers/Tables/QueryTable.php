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
        $tableParams = $table->getStateList();
        if (isset($tableParams['row_id'])) {
            vd($tableParams);

            // perform required action (delete, csv, etc...)

            // clear action params once done
            $table->setState([
                'row_id' => null,
                'row_id_all' => null,
            ]);

            return redirect(request()->fullUrl())->with('success', "Table Action Completed.");
        }

        return view('tables.table-query', Compact('table'));
    }

}
