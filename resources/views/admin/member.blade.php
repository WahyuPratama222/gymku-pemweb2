@extends('layouts.admin')

@section('title', 'Data Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Data Member</h4>
            <small class="text-muted">Semua member terdaftar beserta status membership terakhirnya</small>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">

        @if (empty($members) || count($members) == 0)
            <div class="card bg-white border-0 shadow-sm">
                <div class="card-body text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-danger"></i>
                    Belum ada member terdaftar.
                </div>
            </div>
        @else
        @foreach ($members as $m)
            @php
                $hasMembership = !empty($m->id_registration);
                $status = $m->status ?? null;
                $genderIcon = $m->gender === 'Laki-Laki' ? 'bi-gender-male' : 'bi-gender-female';
                // Ikon gender tetap menggunakan warna informatif asli (Info/Danger) agar mudah dibedakan
                $genderColor = $m->gender === 'Laki-Laki' ? 'text-info' : 'text-danger';

                // Hitung hari tersisa
                $daysRemaining = null;
                if ($hasMembership && $status === 'Active' && $m->expiry_date) {
                    $today = \Carbon\Carbon::today();
                    $expiry = \Carbon\Carbon::parse($m->expiry_date);
                    $daysRemaining = $today->diffInDays($expiry, false);
                    $daysRemaining = $daysRemaining < 0 ? 0 : $daysRemaining;
                }

                // Status badge class
                $statusBadgeClass = match($status) {
                    'Active' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25',
                    'Expired' => 'bg-danger bg-opacity-10 text-danger border-danger border-opacity-25',
                    'Pending' => 'bg-warning bg-opacity-10 text-dark border-warning border-opacity-25',
                    'Cancelled' => 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25',
                    default => 'bg-secondary bg-opacity-10 text-secondary border-secondary border-opacity-25'
                };
            @endphp

                <div class="card bg-white border-0 shadow-sm text-dark">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4 flex-wrap">

                            <div style="min-width: 200px;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                        style="width:36px;height:36px;font-size:.85rem;">
                                        {{ strtoupper(substr($m->member_name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-bold small text-dark">{{ $m->member_name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">{{ $m->email }}</div>
                                        <div class="text-muted" style="font-size:.7rem;">
                                            <i class="bi bi-calendar3 me-1 text-danger"></i>
                                            Bergabung {{ \Carbon\Carbon::parse($m->joined_at)->translatedFormat('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="min-width: 50px;" class="text-center">
                                <i class="bi {{ $genderIcon }} fs-5 {{ $genderColor }}"></i>
                                <div class="text-muted" style="font-size:.7rem;">{{ $m->gender }}</div>
                            </div>

                            <div style="min-width: 160px;">
                                @if ($hasMembership)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 mb-1">{{ $m->package_name }}</span>
                                    <div class="small text-danger fw-bold">Rp {{ number_format($m->price, 0, ',', '.') }}</div>
                                    <div class="text-muted" style="font-size:.7rem;">{{ $m->day_duration }} hari</div>
                                @else
                                    <span class="badge bg-light text-muted border">Belum Daftar Paket</span>
                                    <div class="text-muted mt-1" style="font-size:.72rem;">
                                        <i class="bi bi-info-circle me-1"></i>Belum beli paket
                                    </div>
                                @endif
                            </div>

                            <div class="flex-grow-1">
                                @if ($hasMembership)
                                    <div class="small text-muted">Mulai:
                                        <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($m->start_date)->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <div class="small text-muted">Berakhir:
                                        <span class="text-dark fw-medium">{{ \Carbon\Carbon::parse($m->expiry_date)->translatedFormat('d M Y') }}</span>
                                    </div>
                                @else
                                    <div class="text-muted small italic">— Tidak ada data membership —</div>
                                @endif
                            </div>

                            <div style="min-width: 80px;" class="text-center">
                                @if ($status === 'Active' && $daysRemaining !== null)
                                    <div class="fs-5 fw-bold {{ $daysRemaining <= 7 ? 'text-danger' : 'text-success' }}">
                                        {{ $daysRemaining }}
                                    </div>
                                    <div class="small text-muted" style="font-size: .7rem;">hari tersisa</div>
                                @else
                                    <span class="text-muted small">—</span>
                                @endif
                            </div>

                            <div style="min-width: 100px;" class="text-center">
                                @if (!$hasMembership)
                                    <span class="badge bg-light text-muted border px-3">Tidak Ada</span>
                                @else
                                    <span class="badge border px-3 py-2 {{ $statusBadgeClass }}">
                                        @switch($status)
                                            @case('Active') Aktif @break
                                            @case('Expired') Expired @break
                                            @case('Pending') Pending @break
                                            @case('Cancelled') Dibatalkan @break
                                            @default {{ $status }}
                                        @endswitch
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</div>
@endsection
