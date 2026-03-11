<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Tables\UserTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{

    public function index(Request $request)
    {
        $this->setPageName('User Manager');

        $table = new UserTable();

        return view('pages.users.index', [
            'table' => $table,
            'create' => '/user/create',
        ]);
    }

    public function create()
    {
        $this->setPageName('user.edit|User Create');

        $user = new User();

        return view('pages.users.edit', [
            'mode' => 'create',
            'method' => 'post',
            'action' => '/user',
            'cancelRoute' => '/users',
            'user' => $user,
        ]);
    }

    public function show(User $user)
    {
        $this->setPageName('user.edit|User View');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit',[
            'mode' => 'view',
            'cancelRoute' => '/users',
            'editRoute' => "/user/{$user->id}/edit",
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        $this->setPageName('user.edit|User Edit');
        //Gate::authorize('update', $idea);

        return view('pages.users.edit', [
            'mode' => 'edit',
            'method' => 'patch',
            'action' => '/user/' . $user->id,
            'cancelRoute' => '/users',
            'user' => $user,
        ]);
    }

    public function update(User $user, Request $request)
    {
        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email',
        ]);

        $user->update([
            'name' => $vals['name'],
            'email' => $vals['email'],
        ]);

        return redirect('/user/' . $user->id);
    }

    public function store(Request $request)
    {
        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email|unique:users,email',
            'password' => 'required|min:4|max:64',
        ]);

        Auth::user()->create($vals);

        return redirect('/users');
    }
}
