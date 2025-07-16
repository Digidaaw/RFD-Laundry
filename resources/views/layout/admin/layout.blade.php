<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    @vite('resources/css/app.css') {{-- Tailwind via Vite --}}
</head>
<body class="font-sans bg-gray-100">

    {{-- Header (optional) --}}
    <header class="bg-white shadow-md px-6 py-4">
        <h1 class="text-2xl font-semibold">Admin Panel</h1>
    </header>

    {{-- Main Content --}}
    <main class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer (optional) --}}
    <footer class="bg-white text-center text-sm text-gray-500 py-4">
        &copy; {{ date('Y') }} RFD Admin. All rights reserved.
    </footer>

</body>
</html>
