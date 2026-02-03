<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlumniAuthController extends Controller
{
    /**
     * Show alumni login form.
     */
    public function showLogin()
    {
        if (Auth::guard('alumni')->check()) {
            return redirect()->route('alumni.dashboard');
        }

        return view('auth.alumni.login');
    }

    /**
     * Authenticate alumni (email + password).
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (!Auth::guard('alumni')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password.',
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('alumni.dashboard'));
    }

    /**
     * Logout alumni.
     */
    public function logout(Request $request)
    {
        Auth::guard('alumni')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('alumni.login');
    }
}
