<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function forgot(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate(['email' => ['required', 'email']]);

            Password::sendResetLink($request->only('email'));

            return back()->with('success', 'If that email address is registered, a password reset link has been sent.');
        }

        return view('pages.forgot-password');
    }

    public function reset(Request $request, ?string $token = null)
    {
        if ($request->isMethod('post')) {
            $credentials = $request->validate([
                'token' => ['required'],
                'email' => ['required', 'email'],
                'password' => ['required', 'confirmed', 'min:8', 'max:255'],
            ]);

            $status = Password::reset($credentials, function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            });

            if ($status === Password::PASSWORD_RESET) {
                return redirect(route('login'))->with('success', 'Your password has been reset. You may now log in.');
            }

            return back()->withErrors(['email' => __($status)]);
        }

        return view('pages.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
    }
}
