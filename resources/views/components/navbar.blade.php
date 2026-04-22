<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-secondary shadow-sm">
    <div class="container">

        <!-- Logo -->
        <a href="{{ route('home') }}" class="navbar-brand fw-bold text-warning">
            <i class="bi bi-trophy-fill me-1"></i>
            <span class="text-warning">Gymku</span>
        </a>

        <!-- Tombol toggle untuk mobile -->
        <button class="navbar-toggler border-secondary" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Konten navbar -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-2">

                <!-- Tombol Login -->
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-warning rounded-pill px-3 {{ request()->routeIs('login') ? 'active text-dark' : '' }}"
                        href="{{ route('login') }}">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Login
                    </a>
                </li>

                <!-- Tombol Regist -->
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-warning rounded-pill px-3 {{ request()->routeIs('register') ? 'active text-dark' : '' }}"
                        href="{{ route('register') }}">
                        <i class="bi bi-person-plus-fill me-1"></i> Regist
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>