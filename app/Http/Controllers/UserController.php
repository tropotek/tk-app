<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{

    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect('/');
        }

        if ($request->isMethod('post')) {
            $fieldValues = $request->validate([
                'email' => ['required', 'email'],
                'password' => 'required|min:8|max:255',
            ]);

            if (auth()->attempt($fieldValues)) {
                $request->session()->regenerate();
            }

            return redirect('/');
        }

        return view('login');
    }

    public function logout()
    {
        auth()->logout();
        return redirect('/');
    }

    public function register(UserRegisterRequest $request)
    {
        if (auth()->check()) {
            auth()->logout();
        }

        if ($request->isMethod('post')) {
            $user = User::create($request->validated());
            auth()->login($user);
            return redirect('/')->with('success', 'Registration successful. You are now logged in!');
        }

        return view('register');
    }
}
