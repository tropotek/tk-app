<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function doDefault(Request $request)
    {
        $this->setPageName('Home');

        if (auth()->check()) {
            return redirect(route('dashboard'));
        }

        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect($request->fullUrlWithoutQuery(['alert']))->with($type, "This is a {$type} flash message.");
        }

        return view('pages.home');
    }

}
