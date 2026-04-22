@php
$menuItems = [
    ['route' => 'admin.dashboard', 'icon' => 'bi-speedometer2', 'label' => 'Dashboard'],
    ['route' => 'admin.members', 'icon' => 'bi-people', 'label' => 'Data Member'],
    ['route' => 'admin.packages', 'icon' => 'bi-tags', 'label' => 'Kelola Paket'],
    ['route' => 'admin.payments', 'icon' => 'bi-credit-card', 'label' => 'Data Pembayaran'],
];
@endphp

<div class="d-flex flex-column flex-shrink-0 bg-dark border-end border-secondary"
    style="width: 240px; height: 100vh; overflow-y: auto;">

    <!-- Logo -->
    <a href="{{ route('admin.dashboard') }}"
        class="d-flex align-items-center gap-2 px-3 py-4 text-decoration-none border-bottom border-secondary flex-shrink-0">
        <i class="bi bi-trophy-fill text-warning fs-5"></i>
        <span class="text-warning fw-bold fs-5">Gymku</span>
        <span class="badge bg-warning text-dark ms-auto" style="font-size:.6rem;">Admin</span>
    </a>

    <!-- Nav Menu -->
    <ul class="nav nav-pills flex-column px-2 py-3 gap-1 flex-grow-1">
        @foreach ($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                    class="nav-link d-flex align-items-center gap-2 {{ $isActive ? 'bg-warning fw-bold' : 'text-white' }}"
                    style="{{ $isActive ? 'color: #111827 !important;' : '' }}">
                    <i class="bi {{ $item['icon'] }}"></i>
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <!-- User info + Logout -->
    <div class="border-top border-secondary p-3 flex-shrink-0">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span
                class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                style="width:32px;height:32px;font-size:.8rem;">
                {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
            </span>
            <div class="overflow-hidden">
                <div class="text-white small fw-bold text-truncate">{{ auth()->user()->name ?? 'Admin' }}</div>
                <div class="text-white-50" style="font-size:.7rem;">{{ auth()->user()->email ?? '' }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>

</div>