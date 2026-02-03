<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Alumni Portal')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
    @stack('styles')
</head>

<body class="bg-gray-50 min-h-screen font-serif">

    {{-- Alumni Portal Header --}}
    <header class="bg-white border-b border-gray-200 shadow-sm">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="{{ route('alumni.dashboard') }}" class="flex items-center gap-2 text-[#00285E] font-semibold text-lg">
                    <span>Alumni Portal</span>
                </a>
                <div class="flex items-center gap-4">
                    <span class="text-sm font-medium text-gray-700">{{ auth()->guard('alumni')->user()->first_name ?? '' }} {{ auth()->guard('alumni')->user()->last_name ?? '' }}</span>
                    <form method="POST" action="{{ route('alumni.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium text-gray-700 bg-gray-100 hover:bg-red-50 hover:text-red-600 border border-gray-200 hover:border-red-200 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="w-full max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
        @endif
        @if(session('error'))
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            {{ session('error') }}
        </div>
        @endif
        @yield('content')
    </main>

    @stack('scripts')
</body>

</html>
