<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /* LOGIN PAGE */
    public function index()
    {
        return view('auth.login');
    }

    /* HANDLE EMAIL + PASSWORD */
    public function store(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors([
                'email' => 'Invalid email or password'
            ]);
        }
        
        return redirect()->route('dashboard.index');
        $user = Auth::user();
        Auth::logout(); // login after OTP only

        $otp = rand(100000, 999999);

        session([
            'login_otp'       => $otp,
            'otp_user_id'     => $user->id,
            'otp_expires_at'  => now()->addMinutes(5),
        ]);

        Mail::raw(
            "Your login OTP is: {$otp}\nThis OTP will expire in 5 minutes.",
            function ($message) use ($user) {
                $message->to($user->email)
                    ->subject('Login OTP Verification');
            }
        );

        return redirect()->route('otp.form');
    }

    /* OTP FORM */
    public function showOtp()
    {
        if (!session()->has('login_otp')) {
            return redirect()->route('login');
        }

        return view('auth.verify-otp');
    }

    /* VERIFY OTP */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required',
        ]);

        if (now()->gt(session('otp_expires_at'))) {
            session()->flush();
            return redirect()->route('login')
                ->withErrors(['otp' => 'OTP expired. Please login again.']);
        }

        if ($request->otp != session('login_otp')) {
            return back()->withErrors(['otp' => 'Invalid OTP']);
        }

        Auth::loginUsingId(session('otp_user_id'));

        session()->forget([
            'login_otp',
            'otp_user_id',
            'otp_expires_at'
        ]);

        return redirect()->route('dashboard.index');
    }
}
