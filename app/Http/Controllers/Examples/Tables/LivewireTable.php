<?php

namespace App\Http\Controllers\Examples\Tables;


use App\Http\Controllers\Controller;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;

class LivewireTable extends Controller
{

    public function index(Request $request)
    {
        Breadcrumbs::push('Livewire Table');


        return view('pages.examples.tables.table-livewire', []);
    }

}
