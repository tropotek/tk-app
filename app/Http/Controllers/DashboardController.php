<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use Illuminate\Http\Request;
use Tk\Support\Facades\Breadcrumbs;

class DashboardController extends Controller
{

    public function doDefault(Request $request)
    {
        Breadcrumbs::push('Dashboard');


        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect($request->fullUrlWithoutQuery(['alert']))->with($type, "This is a {$type} flash message.");
        }

        return view('pages.dashboard');
    }

}
