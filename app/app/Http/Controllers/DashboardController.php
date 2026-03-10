<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function doDefault(Request $request)
    {
        $this->setPageName('Dashboard');

        $attr = new \Illuminate\View\ComponentAttributeBag();
        $attr = $attr->class('test test2');
        $attr = $attr->class('test test3');
        vd($attr->__toString());

        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect($request->fullUrlWithoutQuery(['alert']))->with($type, "This is a {$type} flash message.");
        }

        return view('pages.dashboard');
    }

}
