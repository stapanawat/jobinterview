<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Interview System') }} — HR Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
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

                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">{{ now()->format('d M Y') }}</span>
                    <div class="w-px h-6 bg-gray-200"></div>
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-2 text-sm font-medium text-gray-700 hover:text-brand-600 transition-colors">
                                <div
                                    class="w-8 h-8 rounded-full gradient-brand flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                                <span>{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link :href="route('profile.edit')">{{ __('Profile') }}</x-dropdown-link>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
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