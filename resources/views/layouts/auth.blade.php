<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Auth')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Tailwind --}}
    @vite('resources/css/app.css')
</head>

<body class=" bg-gray-100">

    <div >
        @yield('content')
    </div>

</body>

</html>