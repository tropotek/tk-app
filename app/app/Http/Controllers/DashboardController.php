<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function doDefault(Request $request)
    {
        $this->setTitle('Dashboard');


        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect('/')->with($type, "This is a {$type} flash message.");
        }

        return view('pages.dashboard');
    }

}
