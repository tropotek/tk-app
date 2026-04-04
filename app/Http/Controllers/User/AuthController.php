<?php

namespace App\Http\Controllers\User;

use App\Enum\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        if (auth()->check()) {
            return redirect(route('dashboard'));
        }

        if ($request->isMethod('post')) {
            $fieldValues = $request->validate([
                'email' => ['required', 'email'],
                'password' => 'required|min:8|max:255',
            ]);

            if (auth()->attempt($fieldValues, $request->has('remember'))) {
                $request->session()->regenerate();
            }

            return redirect(route('dashboard'));
        }

        return view('pages.login');
    }

    public function logout()
    {
        auth()->logout();
        session()->flush();
        return redirect(route('home'));
    }

    public function register(UserRegisterRequest $request)
    {
        if (auth()->check()) {
            auth()->logout();
        }

        if ($request->isMethod('post')) {

            $user = User::create($request->validated());
            $user->assignRole(Roles::Member->value);

            auth()->login($user);
            return redirect(route('dashboard'))->with('success', 'Registration successful. You are now logged in!');
        }

        return view('pages.register');
    }
}
