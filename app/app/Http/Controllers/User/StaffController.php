<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\User;
use App\Tables\StaffTable;
use App\Tables\UserTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{

    public function index(Request $request)
    {
        $this->setPageName('Staff Manager');

        $table = new StaffTable();

        return view('pages.users.index', [
            'table' => $table,
            'create' => '/staff/create',
        ]);
    }

    public function create()
    {
        $this->setPageName('staff.edit|Staff Create');

        $user = new Staff();

        return view('pages.users.edit', [
            'mode' => 'create',
            'method' => 'post',
            'action' => '/staff',
            'user' => $user,
        ]);
    }

    public function show(Staff $user)
    {
        $this->setPageName('staff.edit|Staff View');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit',[
            'mode' => 'view',
            'user' => $user,
        ]);
    }

    public function edit(Staff $user)
    {
        $this->setPageName('staff.edit|Staff Edit');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit', [
            'mode' => 'edit',
            'method' => 'patch',
            'action' => '/staff/' . $user->id,
            'user' => $user,
        ]);
    }

    public function update(Staff $user, Request $request)
    {

        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email',
        ]);

        $user->update([
            'name' => $vals['name'],
            'email' => $vals['email'],
        ]);

        return redirect('/staff/' . $user->id);
    }

    public function store(Request $request)
    {
        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email|unique:users,email',
            'password' => 'required|min:4|max:64',
        ]);

        Staff::create($vals);

        return redirect('/staff');
    }
}
