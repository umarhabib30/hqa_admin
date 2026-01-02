<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Dashboard')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 min-h-screen " x-data="{ sidebarOpen: false }">

    <div class="flex min-h-screen">

        {{-- SIDEBAR --}}
        <x-sidebar />

        {{-- MAIN AREA --}}
        <div class="flex-1 flex flex-col">

            {{-- TOPBAR --}}
            <x-topBar />

            {{-- PAGE CONTENT --}}
            <main class="flex-1 p-6 font-serif">
                @yield('content')
            </main>

        </div>

    </div>
    <script src="//unpkg.com/alpinejs" defer></script>

</body>

</html>