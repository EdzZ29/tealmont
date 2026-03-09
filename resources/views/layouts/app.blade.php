<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tealmont Image Organizer')</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inria-sans:300,300i,400,400i,700,700i" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans antialiased min-h-screen">
    @auth
    <nav class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <img src="{{ asset('image/tealmont-logo.jpg') }}" alt="Tealmont" class="h-8 w-auto">
                        <span class="text-xl font-bold text-gray-900">Tealmont</span>
                    </a>
                    <div class="hidden sm:flex items-center ml-8 gap-6">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium {{ request()->routeIs('dashboard') ? 'text-teal-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">Dashboard</a>
                        <a href="{{ route('albums.index') }}" class="text-sm font-medium {{ request()->routeIs('albums.*') ? 'text-teal-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">My Albums</a>
                        <a href="{{ route('shared.index') }}" class="text-sm font-medium {{ request()->routeIs('shared.*') ? 'text-teal-600' : 'text-gray-500 hover:text-gray-700' }} transition-colors">Shared</a>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <span class="text-sm text-gray-500">
                        {{ Auth::user()->name }}
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ Auth::user()->isAdmin() ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 transition-colors">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <main>
        @yield('content')
    </main>
</body>
</html>
