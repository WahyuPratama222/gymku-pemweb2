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
                <a href="/" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-gym-primary to-amber-500 flex items-center justify-center shadow-lg shadow-gym-primary/20 group-hover:shadow-gym-primary/40 transition-shadow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <span class="text-xl font-bold tracking-tight">
                        Gym<span class="text-gym-primary">ku</span>
                    </span>
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
                        <button type="submit"
                                id="btn-logout"
                                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-lg
                                       text-gym-text-muted hover:text-gym-error
                                       bg-gym-card hover:bg-gym-error/10
                                       border border-gym-border hover:border-gym-error/30
                                       transition-all duration-200 cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div id="flash-success" class="fixed top-20 right-4 z-50 animate-fade-in-up">
            <div class="flex items-center gap-3 px-5 py-3 rounded-xl bg-gym-success/10 border border-gym-success/30 text-gym-success backdrop-blur-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span class="text-sm font-medium">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div id="flash-error" class="fixed top-20 right-4 z-50 animate-fade-in-up">
            <div class="flex items-center gap-3 px-5 py-3 rounded-xl bg-gym-error/10 border border-gym-error/30 text-gym-error backdrop-blur-sm">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    {{-- Main Content --}}
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    {{-- Auto-dismiss flash --}}
    <script>
        setTimeout(() => {
            document.querySelectorAll('#flash-success, #flash-error').forEach(el => {
                el.style.transition = 'opacity 0.5s ease-out';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    </script>
</body>
</html>
