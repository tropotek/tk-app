<?php

namespace App\Http\Controllers\Examples\Tables;


use App\Http\Controllers\Controller;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;

class LivewireTable extends Controller
{

    public function index(Request $request)
    {
        $this->setPageName('Livewire 4 Table');


        return view('pages.examples.tables.table-livewire', []);
    }

}
