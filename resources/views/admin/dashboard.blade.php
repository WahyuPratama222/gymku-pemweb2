@extends('layouts.admin')

@section('title', 'Dashboard Admin — Gymku')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Dashboard Admin</h4>
            <small class="text-white-50">
                Selamat datang, {{ auth()->user()->name }}
            </small>
        </div>
        <span class="text-white-50 small">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">

        <div class="col-sm-6 col-xl-4">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-primary bg-opacity-25">
                        <i class="bi bi-people-fill fs-4 text-primary"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $summary['total_members'] ?? 0 }}</div>
                        <div class="small text-white-50">Total Member</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-success bg-opacity-25">
                        <i class="bi bi-patch-check-fill fs-4 text-success"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $summary['active_memberships'] ?? 0 }}</div>
                        <div class="small text-white-50">Membership Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-warning bg-opacity-25">
                        <i class="bi bi-currency-dollar fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">Rp {{ number_format($summary['income_today'] ?? 0, 0, ',', '.') }}</div>
                        <div class="small text-white-50">Pendapatan Hari Ini</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Row 2: Ringkasan + Aksi Cepat -->
    <div class="row g-3">

        <div class="col-md-6">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body p-4">
                    <h6 class="text-white-50 mb-3">
                        <i class="bi bi-graph-up me-2"></i>Ringkasan Bulan Ini
                    </h6>
                    <div class="d-flex flex-column gap-2">
                        <div class="d-flex justify-content-between border-bottom border-secondary pb-2">
                            <span class="text-white-50 small">Pendapatan</span>
                            <span class="fw-bold text-warning">Rp {{ number_format($summary['income_this_month'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex justify-content-between border-bottom border-secondary pb-2">
                            <span class="text-white-50 small">Paket Tersedia</span>
                            <span class="fw-bold text-white">{{ $summary['active_packages'] ?? 0 }} Paket Aktif</span>
                        </div>
                        <div class="d-flex justify-content-between pt-1">
                            <span class="text-white-50 small">Membership Kedaluwarsa</span>
                            <span class="fw-bold text-danger">{{ $summary['expired_memberships'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body p-4">
                    <h6 class="text-white-50 mb-3">
                        <i class="bi bi-hourglass-split me-2"></i>Pembayaran Pending
                    </h6>

                    @if (empty($pendingPayments) || count($pendingPayments) == 0)
                        <div class="text-center py-3 text-white-50">
                            <i class="bi bi-check-circle fs-3 d-block mb-2 text-success"></i>
                            <small>Semua pembayaran sudah lunas</small>
                        </div>
                    @else
                        <div class="d-flex flex-column gap-0">
                            @foreach ($pendingPayments as $index => $pp)
                                <div class="d-flex align-items-center gap-3 py-2 {{ $index < count($pendingPayments) - 1 ? 'border-bottom border-secondary' : '' }}">
                                    <span class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                        style="width:30px;height:30px;font-size:.75rem;">
                                        {{ strtoupper(substr($pp['member_name'], 0, 1)) }}
                                    </span>
                                    <div class="flex-grow-1">
                                        <div class="small fw-semibold">{{ $pp['member_name'] }}</div>
                                        <div class="text-white-50" style="font-size:.72rem;">{{ $pp['package_name'] }}</div>
                                    </div>
                                    <div class="text-warning fw-bold small">Rp {{ number_format($pp['amount'], 0, ',', '.') }}</div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.payments') }}" class="btn btn-outline-warning btn-sm rounded-pill px-3">
                                <i class="bi bi-arrow-right me-1"></i>Lihat Semua
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</div>
@endsection