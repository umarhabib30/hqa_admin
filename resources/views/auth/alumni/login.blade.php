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
                    <input type="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 font-serif focus:outline-none focus:ring-2 focus:ring-[#00285E] transition"
                        placeholder="••••••••">
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

@endsection
