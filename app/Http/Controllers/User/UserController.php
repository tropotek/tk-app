<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tk\Support\Facades\Breadcrumbs;

class UserController extends Controller
{

    public function create()
    {
        Breadcrumbs::push('user.edit|User Create');

        $user = new User();

        return view('pages.users.edit1', [
            'mode' => 'create',
            'method' => 'post',
            'action' => '/user',
            'cancelRoute' => '/users',
            'user' => $user,
        ]);
    }

    public function show(User $user)
    {
        Breadcrumbs::push('user.edit|User View');

        return view('pages.users.edit1',[
            'mode' => 'view',
            'cancelRoute' => '/users',
            'editRoute' => "/user/{$user->id}/edit",
            'user' => $user,
        ]);
    }

    public function edit(User $user)
    {
        Breadcrumbs::push('user.edit|User Edit');

        return view('pages.users.edit1', [
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
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string'],
        ]);

        $user->update([
            'name' => $vals['name'],
            'email' => $vals['email'],
        ]);

        // TODO: Remove this when permission system fully implemented
        if ($user->id != 1) {
            $user->syncRoles($vals['roles']);
        }

        return redirect('/user/' . $user->id);
    }

    public function store(Request $request)
    {
        $vals = $request->validate([
            'name' => ['required', 'min:3'],
            'email' => 'email|unique:users,email',
            'password' => 'required|min:4|max:64',
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string'],
        ]);

        $user = Auth::user()->create($vals);
        $user->syncRoles($vals['roles']);

        return redirect('/users');
    }
}
