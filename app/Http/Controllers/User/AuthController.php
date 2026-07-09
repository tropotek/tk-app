<?php

namespace App\Http\Controllers\User;

use App\Enum\Roles;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

            $throttleKey = Str::lower($fieldValues['email']).'|'.$request->ip();

            if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
                $seconds = RateLimiter::availableIn($throttleKey);

                return back()->withErrors([
                    'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
                ])->onlyInput('email');
            }

            if (auth()->attempt($fieldValues, $request->has('remember'))) {
                RateLimiter::clear($throttleKey);
                $request->session()->regenerate();

                return redirect(route('dashboard'));
            }

            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'email' => 'These credentials do not match our records.',
            ])->onlyInput('email');
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

            $user = User::create([...$request->validated(), 'role' => Roles::Member]);

            auth()->login($user);

            return redirect(route('dashboard'))->with('success', 'Registration successful. You are now logged in!');
        }

        return view('pages.register');
    }
}
