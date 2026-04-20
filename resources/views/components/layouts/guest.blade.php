<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Gymku — Platform manajemen gym modern untuk member dan admin.">

    <title>{{ $title ?? 'Gymku' }} — Gymku</title>

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gym-darker text-gym-text font-sans antialiased">

    {{-- Flash Messages --}}
    @if (session('success'))
        <x-flash type="success" :message="session('success')" />
    @endif

    @if (session('error'))
        <x-flash type="error" :message="session('error')" />
    @endif

    {{ $slot }}
</body>
</html>