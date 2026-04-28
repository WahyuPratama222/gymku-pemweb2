<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — Gymku')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #f8f9fa; /* Berubah ke abu-abu sangat muda (Light Gray) */
            overflow-x: hidden;
        }
    </style>

    @stack('styles')
</head>
<body class="bg-light"> <div class="d-flex" style="height: 100vh; overflow: hidden;">

        @include('components.sidebar-admin')

        <div class="d-flex flex-column flex-grow-1">
            <main class="flex-grow-1" style="overflow-y: auto;">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
