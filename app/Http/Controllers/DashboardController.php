<?php

namespace App\Http\Controllers;

use App\Enum\Roles;
use Illuminate\Http\Request;

class DashboardController extends Controller
{

    public function doDefault(Request $request)
    {
        $this->setPageName('Dashboard');


        if ($request->has('alert')) {
            $type = $request->input('alert');
            return redirect($request->fullUrlWithoutQuery(['alert']))->with($type, "This is a {$type} flash message.");
        }

        return view('pages.dashboard');
    }

}
