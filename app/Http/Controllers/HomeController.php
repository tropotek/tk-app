<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Tk\Support\Facades\Breadcrumbs;

class HomeController extends Controller
{

    public function doDefault(Request $request)
    {
        $this->setPageTitle('Dashboard');

        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect('/')->with($type, "This is a {$type} flash message.");
        }

        return view('home');
    }

}
