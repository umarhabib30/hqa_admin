@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')

<div class="min-h-screen flex flex-col lg:flex-row">

    <div class="w-full lg:w-1/2 bg-white flex items-start lg:items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold mb-2 text-[#00285E] font-serif">
                Reset Password
            </h2>
            <p class="text-gray-500 mb-8 font-serif">
                Enter your new password below.
            </p>

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">Email</label>
                    <input type="email" value="{{ $email }}" disabled
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif bg-gray-50 text-gray-600">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">New Password</label>
                    <input type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                        placeholder="••••••••">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">Confirm Password</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                        placeholder="••••••••">
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl border-2 border-[#00285E] text-[#00285E] font-semibold text-lg font-serif hover:bg-[#00285E] hover:text-white transition-all duration-300 active:scale-95">
                    Reset Password
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-500 text-center">
                <a href="{{ route('login') }}" class="text-[#00285E] font-medium hover:underline">Back to login</a>
            </p>
        </div>
    </div>

    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-[#BCDDFC] to-[#00285E] items-center justify-center text-white px-6">
        <div class="text-center max-w-sm">
            <img src="{{ asset('image/logo.webp') }}" alt="HQA School Logo" class="mx-auto mb-6 h-72 w-auto object-contain">
            <h2 class="text-2xl font-semibold mb-2 font-serif">HQA School Dashboard</h2>
            <p class="text-base opacity-90 leading-relaxed font-serif">
                Choose a strong password for your admin account.
            </p>
        </div>
    </div>

</div>

@endsection
