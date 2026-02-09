@extends('layouts.auth')

@section('title', 'Alumni Forgot Password')

@section('content')

<div class="min-h-screen flex flex-col lg:flex-row">

    <div class="w-full lg:w-1/2 bg-white flex items-start lg:items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold mb-2 text-[#00285E] font-serif">
                Forgot Password
            </h2>
            <p class="text-gray-500 mb-8 font-serif">
                Enter your alumni email and we'll send you a link to reset your password.
            </p>

            @if (session('status'))
                <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-6">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm mb-6">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('alumni.password.email') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                        placeholder="your@email.com">
                </div>

                <button type="submit"
                    class="w-full py-3 rounded-xl border-2 border-[#00285E] text-[#00285E] font-semibold text-lg font-serif hover:bg-[#00285E] hover:text-white transition-all duration-300 active:scale-95">
                    Send Password Reset Link
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-500 text-center">
                <a href="{{ route('alumni.login') }}" class="text-[#00285E] font-medium hover:underline">Back to alumni login</a>
            </p>
        </div>
    </div>

    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-[#BCDDFC] to-[#00285E] items-center justify-center text-white px-6">
        <div class="text-center max-w-sm">
            <img src="{{ asset('image/logo.webp') }}" alt="HQA School Logo" class="mx-auto mb-6 h-72 w-auto object-contain">
            <h2 class="text-2xl font-semibold mb-2 font-serif">HQA Alumni</h2>
            <p class="text-base opacity-90 leading-relaxed font-serif">
                Use the email you registered with for the alumni portal.
            </p>
        </div>
    </div>

</div>

@endsection
