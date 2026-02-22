<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ExamplesController extends Controller
{

    public function index(Request $request)
    {


        return view('examples');
    }

}