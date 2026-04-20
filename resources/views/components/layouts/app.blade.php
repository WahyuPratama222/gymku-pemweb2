<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Gymku — Platform manajemen gym modern.">

    <title>{{ $title ?? 'Dashboard' }} — Gymku</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gym-darker text-gym-text font-sans antialiased">

    {{-- Navbar --}}
    <nav class="sticky top-0 z-40 border-b border-gym-border bg-gym-dark/80 backdrop-blur-xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="/" class="group">
                    <x-logo size="md" />
                </a>

                {{-- User Menu --}}
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-2 text-sm text-gym-text-muted">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gym-primary/20 to-amber-500/20 border border-gym-border flex items-center justify-center">
                            <span class="text-xs font-semibold text-gym-primary">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="font-medium text-gym-text text-sm">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-gym-text-muted capitalize">{{ Auth::user()->role }}</span>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-button variant="secondary" size="sm" type="submit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </x-button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-flash type="success" :message="session('success')" />
    @endif

    @if (session('error'))
        <x-flash type="error" :message="session('error')" />
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>
</body>
</html>