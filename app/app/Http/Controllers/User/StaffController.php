<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Tables\StaffTable;
use Illuminate\Http\Request;

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
            'cancelRoute' => '/staff',
            'user' => $user,
        ]);
    }

    public function show(Staff $staff)
    {
        $this->setPageName('staff.edit|Staff View');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit',[
            'mode' => 'view',
            'cancelRoute' => '/staff',
            'editRoute' => "/staff/{$staff->id}/edit",
            'user' => $staff,
        ]);
    }

    public function edit(Staff $staff)
    {
        $this->setPageName('staff.edit|Staff Edit');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit', [
            'mode' => 'edit',
            'method' => 'patch',
            'action' => '/staff/' . $staff->id,
            'cancelRoute' => '/staff',
            'user' => $staff,
        ]);
    }

    public function update(Staff $staff, Request $request)
    {
        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email',
        ]);

        $staff->update([
            'name' => $vals['name'],
            'email' => $vals['email'],
        ]);

        return redirect('/staff/' . $staff->id);
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
