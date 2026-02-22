<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HomeController extends Controller
{

    public function doDefault(Request $request)
    {

        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect('/')->with($type, "This is a {$type} flash message.");
        }
vd($request->all());
        return view('home');
    }

}
