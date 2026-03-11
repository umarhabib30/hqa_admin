@extends('layouts.auth')

@section('title', 'Alumni Login')

@section('content')

<div class="min-h-screen flex flex-col lg:flex-row">

    {{-- Left: Login Form --}}
    <div class="w-full lg:w-1/2 bg-white flex items-start lg:items-center justify-center px-6 py-10">
        <div class="w-full max-w-md">
            <h2 class="text-3xl font-bold mb-2 text-[#00285E] font-serif">
                Alumni Portal
            </h2>
            <p class="text-gray-500 mb-8 font-serif">
                Sign in with your alumni account
            </p>

            @if (session('status'))
                <div class="rounded-lg bg-green-50 border border-green-200 text-green-700 px-4 py-3 text-sm mb-6">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('alumni.login.store') }}" class="space-y-5">
                @csrf

                @if($errors->any())
                <div class="rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                        placeholder="your@email.com">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-600 mb-1 font-serif">Password</label>
                    <div class="relative">
                        <input id="alumni-password" type="password" name="password" required
                            class="w-full px-4 py-3 pr-12 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                            placeholder="********">
                        <button type="button"
                            class="password-toggle absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-[#00285E] focus:outline-none focus:ring-2 focus:ring-[#00285E]/30 rounded-r-lg"
                            data-target="alumni-password"
                            aria-label="Show password">
                            <svg class="eye-open h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="eye-closed hidden h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 002.25 12c1.391 4.173 5.326 7.5 9.75 7.5 1.918 0 3.73-.628 5.196-1.698M6.228 6.228A10.45 10.45 0 0112 4.5c4.423 0 8.358 3.327 9.75 7.5a10.524 10.524 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        <a href="{{ route('alumni.password.request') }}" class="text-[#00285E] font-medium hover:underline">Forgot password?</a>
                    </p>
                </div>

                <label class="flex items-center gap-2 text-sm text-gray-600">
                    <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#00285E] focus:ring-[#00285E]">
                    Remember me
                </label>

                <button type="submit"
                    class="w-full py-3 rounded-xl border-2 border-[#00285E] text-[#00285E] font-semibold text-lg font-serif hover:bg-[#00285E] hover:text-white transition-all duration-300 active:scale-95">
                    Sign In
                </button>
            </form>

            <p class="mt-6 text-sm text-gray-500 text-center">
                Use the email and password set for your alumni profile. Contact admin if you need access.
            </p>
        </div>
    </div>

    {{-- Right: Branding --}}
    <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-[#BCDDFC] to-[#00285E] items-center justify-center text-white px-6">
        <div class="text-center max-w-sm">
            <img src="{{ asset('image/logo.webp') }}" alt="HQA School Logo" class="mx-auto mb-6 h-72 w-auto object-contain">
            <h2 class="text-2xl font-semibold mb-2 font-serif">HQA Alumni</h2>
            <p class="text-base opacity-90 leading-relaxed font-serif">
                Stay connected with your alma mater. Access your profile and alumni resources.
            </p>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.password-toggle').forEach(function(button) {
            button.addEventListener('click', function() {
                const input = document.getElementById(button.dataset.target);
                if (!input) return;

                const isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                button.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
                const eyeOpen = button.querySelector('.eye-open');
                const eyeClosed = button.querySelector('.eye-closed');
                if (eyeOpen && eyeClosed) {
                    eyeOpen.classList.toggle('hidden', isHidden);
                    eyeClosed.classList.toggle('hidden', !isHidden);
                }
            });
        });
    });
</script>

@endsection
