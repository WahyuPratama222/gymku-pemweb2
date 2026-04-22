@extends('layouts.member')

@section('title', 'Dashboard Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Dashboard Member</h4>
            <small class="text-white-50">Selamat datang, {{ auth()->user()->name }}</small>
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
                    <div class="p-3 rounded-3 {{ $activeMembership ? 'bg-success bg-opacity-25' : 'bg-danger bg-opacity-25' }}">
                        <i class="bi bi-patch-check-fill fs-4 {{ $activeMembership ? 'text-success' : 'text-danger' }}"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ $activeMembership ? 'Aktif' : 'Tidak Aktif' }}</div>
                        <div class="small text-white-50">Status Membership</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-warning bg-opacity-25">
                        <i class="bi bi-hourglass-split fs-4 text-warning"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $activeMembership ? $daysRemaining : '-' }}</div>
                        <div class="small text-white-50">Hari Tersisa</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-4">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-info bg-opacity-25">
                        <i class="bi bi-tags fs-4 text-info"></i>
                    </div>
                    <div>
                        <div class="fs-5 fw-bold">{{ $activeMembership ? $activeMembership->package_name : '-' }}</div>
                        <div class="small text-white-50">Paket Aktif</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Membership Aktif + Pembayaran Terakhir -->
    <div class="row g-3">

        <div class="col-md-5">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-3">
                        <i class="bi bi-patch-check me-2"></i>Membership Aktif Saya
                    </h6>

                    @if ($activeMembership)
                        <div class="p-3 rounded-3 border border-success border-opacity-25 bg-success bg-opacity-10">
                            <div class="fw-bold text-warning fs-6 mb-1">
                                {{ $activeMembership->package_name }}
                            </div>
                            <div class="small text-white-50 mb-2">
                                {{ $activeMembership->day_duration }} hari
                                · Rp {{ number_format($activeMembership->price, 0, ',', '.') }}
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-white-50">Mulai</span>
                                <span class="text-white">{{ \Carbon\Carbon::parse($activeMembership->start_date)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-white-50">Berakhir</span>
                                <span class="text-white">{{ \Carbon\Carbon::parse($activeMembership->expiry_date)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small mt-1">
                                <span class="text-white-50">Sisa</span>
                                <span class="fw-bold {{ $daysRemaining <= 7 ? 'text-danger' : 'text-success' }}">
                                    {{ $daysRemaining }} hari
                                </span>
                            </div>
                            @php
                                $totalDays = $activeMembership->day_duration;
                                $percent = $totalDays > 0 ? max(0, min(100, round(($daysRemaining / $totalDays) * 100))) : 0;
                                $barColor = $percent > 50 ? 'bg-success' : ($percent > 20 ? 'bg-warning' : 'bg-danger');
                            @endphp
                            <div class="progress mt-2" style="height:6px;">
                                <div class="progress-bar {{ $barColor }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-x-circle text-danger fs-1 mb-2 d-block"></i>
                            <p class="text-white-50 small mb-3">Kamu belum memiliki membership aktif.</p>
                            <a href="{{ route('member.packages') }}" class="btn btn-warning btn-sm fw-bold text-dark">
                                <i class="bi bi-tags me-1"></i> Lihat Paket
                            </a>
                        </div>
                    @endif

                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body">
                    <h6 class="text-white-50 mb-3">
                        <i class="bi bi-receipt me-2"></i>Pembayaran Terakhir
                    </h6>

                    @if (empty($recentPayments) || count($recentPayments) == 0)
                        <div class="text-center py-4">
                            <i class="bi bi-receipt text-white-50 fs-1 mb-2 d-block"></i>
                            <p class="text-white-50 small mb-0">Belum ada riwayat pembayaran.</p>
                        </div>
                    @else
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                            @foreach ($recentPayments as $pay)
                                <li class="d-flex justify-content-between align-items-center border-bottom border-secondary pb-2">
                                    <div>
                                        <div class="small fw-semibold">{{ $pay->package_name }}</div>
                                        <div class="text-white-50" style="font-size:.75rem;">
                                            {{ \Carbon\Carbon::parse($pay->payment_date)->translatedFormat('d M Y') }}
                                            · {{ $pay->payment_method }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small fw-bold text-warning">
                                            Rp {{ number_format($pay->amount, 0, ',', '.') }}
                                        </div>
                                        @php
                                            $badge = match($pay->payment_status) {
                                                'Lunas' => 'bg-success',
                                                'Belum Lunas' => 'bg-warning text-dark',
                                                'Gagal' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }} mt-1" style="font-size:.65rem;">
                                            {{ $pay->payment_status }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                </div>
            </div>
        </div>

    </div>

</div>
@endsection