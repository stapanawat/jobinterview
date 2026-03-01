<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PKS Recruit') }} — Petkaset.co</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="flex min-h-screen bg-gray-50">

        <!-- Sidebar -->
        @include('layouts.navigation')

        <!-- Main Content -->
        <div class="main-content flex-1 flex flex-col">

            <!-- Top Bar -->
            <header
                class="bg-white border-b border-gray-100 px-8 py-4 flex items-center justify-between sticky top-0 z-40">
                <div>
                    @isset($header)
                        {{ $header }}
                    @endisset
                </div>

                <div class="flex items-center gap-3">
                    <div
                        class="w-8 h-8 rounded-full gradient-brand flex items-center justify-center text-white text-xs font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <span class="text-sm font-medium text-gray-700">{{ Auth::user()->name }}</span>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>

</html>