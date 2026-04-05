<?php

namespace App\Http\Controllers\Examples\Tables;


use App\Http\Controllers\Controller;
use App\Tables\IdeaTable;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;

class LivewireTwoTable extends Controller
{

    public function index(Request $request)
    {
        Breadcrumbs::push('Livewire 2 Table');


        return view('pages.examples.tables.table-livewire-two', []);
    }

}
