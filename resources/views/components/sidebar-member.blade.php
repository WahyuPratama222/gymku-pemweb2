@php
$menuItems = [
    ['route' => 'member.dashboard', 'icon' => 'bi-house-door', 'label' => 'Dashboard'],
    ['route' => 'member.packages', 'icon' => 'bi-tags', 'label' => 'Paket Gym'],
    ['route' => 'member.payments', 'icon' => 'bi-receipt', 'label' => 'Riwayat Pembayaran'],
    ['route' => 'member.progress', 'icon' => 'bi-graph-up-arrow', 'label' => 'Progress Saya'],
];
@endphp

<div class="d-flex flex-column flex-shrink-0 bg-white border-end border-light shadow-sm"
    style="width: 240px; height: 100vh; overflow-y: auto;">

    <a href="{{ route('member.dashboard') }}"
        class="d-flex align-items-center gap-2 px-3 py-4 text-decoration-none border-bottom border-light flex-shrink-0">
        <i class="bi bi-trophy-fill text-danger fs-5"></i>
        <span class="text-danger fw-bold fs-5">Gymku</span>
        <span class="badge bg-danger text-white ms-auto" style="font-size:.6rem;">Member</span>
    </a>

    <ul class="nav nav-pills flex-column px-2 py-3 gap-1 flex-grow-1">
        @foreach ($menuItems as $item)
            @php $isActive = request()->routeIs($item['route']); @endphp
            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                    class="nav-link d-flex align-items-center gap-2 {{ $isActive ? 'bg-danger fw-bold text-white shadow-sm' : 'text-dark' }}">
                    <i class="bi {{ $item['icon'] }} {{ $isActive ? '' : 'text-muted' }}"></i>
                    {{ $item['label'] }}
                </a>
            </li>
        @endforeach
    </ul>

    <div class="border-top border-light p-3 flex-shrink-0 bg-light bg-opacity-50">
        <div class="d-flex align-items-center gap-2 mb-3">
            <span
                class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                style="width:32px;height:32px;font-size:.8rem;">
                {{ strtoupper(substr(auth()->user()->name ?? 'M', 0, 1)) }}
            </span>
            <div class="overflow-hidden">
                <div class="text-dark small fw-bold text-truncate">{{ auth()->user()->name ?? 'Member' }}</div>
                <div class="text-muted" style="font-size:.7rem;">{{ auth()->user()->email ?? '' }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm w-100 fw-bold">
                <i class="bi bi-box-arrow-right me-1"></i>Logout
            </button>
        </form>
    </div>

</div>
