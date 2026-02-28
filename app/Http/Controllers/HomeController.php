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

        $url = $request->url();
        $url = url()->query($url, ['test' => 'A Test String']);
        vd($url);
        $url = url()->query($url, ['test' => null]);
        vd($url);


        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect('/')->with($type, "This is a {$type} flash message.");
        }

        return view('home');
    }

}
