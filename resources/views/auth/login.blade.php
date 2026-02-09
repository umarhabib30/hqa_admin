@extends('layouts.auth')

@section('title', 'Login')

@section('content')

<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- LEFT : LOGIN -->
    <div class="w-full lg:w-1/2 bg-white
                flex items-start lg:items-center
                justify-center
                px-6 py-10">

        <div class="w-full max-w-md">

            <h2 class="text-3xl font-bold mb-2 text-[#00285E] font-serif">
                Welcome Back
            </h2>

            <p class="text-gray-500 mb-8 font-serif">
                Login to continue
            </p>

            @if (session('status'))
                <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST"
                action="{{ route('login.store') }}"
                class="space-y-5">
                @csrf

                <!-- EMAIL -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">
                        Email
                    </label>
                    <input type="email" name="email" required
                        class="w-full px-4 py-3 rounded-lg
                               border border-gray-300 font-serif
                               focus:outline-none focus:ring-2 focus:ring-[#00285E]
                               transition">
                </div>

                <!-- PASSWORD -->
                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">
                        Password
                    </label>
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg
                               border border-gray-300 font-serif
                               focus:outline-none focus:ring-2 focus:ring-[#00285E]
                               transition">
                    <p class="mt-1 text-sm text-gray-500">
                        <a href="{{ route('password.request') }}" class="text-[#00285E] font-medium hover:underline">Forgot password?</a>
                    </p>
                </div>

                <!-- BUTTON -->
                <button type="submit"
                    class="w-full py-3 rounded-xl
                           border-2 border-[#00285E]
                           text-[#00285E] font-semibold text-lg font-serif
                           hover:bg-[#00285E] hover:text-white
                           transition-all duration-300
                           active:scale-95">
                    Login
                </button>

            </form>

        </div>
    </div>

    <!-- RIGHT : LOGO -->
    <div class="hidden lg:flex w-1/2
                bg-gradient-to-br from-[#BCDDFC] to-[#00285E]
                items-center justify-center
                text-white px-6">

        <div class="text-center max-w-sm">

            <img
                src="{{ asset('image/logo.webp') }}"
                alt="HQA School Logo"
                class="mx-auto mb-6 h-72 w-auto object-contain" />

            <h2 class="text-2xl font-semibold mb-2 font-serif">
                HQA School Dashboard
            </h2>

            <p class="text-base opacity-90 leading-relaxed font-serif">
                Manage students, teachers, achievements, and school activities
                from one secure platform.
            </p>

        </div>
    </div>

</div>

@endsection