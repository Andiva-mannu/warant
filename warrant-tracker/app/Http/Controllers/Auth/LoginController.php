<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // create a personal access token so the frontend can use token-based auth
            try {
                $token = $request->user()->createToken('web-token')->plainTextToken;
                // Flash token into session so the layout can persist it into localStorage
                $request->session()->flash('api_token', $token);
            } catch (\Throwable $e) {
                // If token creation fails for any reason, continue with session login.
            }

            return redirect()->intended(route('users.index'));
        }

        return back()->withErrors(['email' => 'These credentials do not match our records.'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
