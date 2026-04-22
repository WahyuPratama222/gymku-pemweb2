@extends('layouts.admin')

@section('title', 'Data Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Data Member</h4>
            <small class="text-white-50">Semua member terdaftar beserta status membership terakhirnya</small>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-3">

        @if (empty($members) || count($members) == 0)
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white">
                <div class="card-body text-center py-5 text-white-50">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Belum ada member terdaftar.
                </div>
            </div>
        @else
            @foreach ($members as $m)
                @php
                    $hasMembership = !empty($m->id_registration);
                    $status = $m->status ?? null;
                    $genderIcon = $m->gender === 'Laki-Laki' ? 'bi-gender-male' : 'bi-gender-female';
                    $genderColor = $m->gender === 'Laki-Laki' ? 'text-info' : 'text-danger';
                    
                    // Hitung hari tersisa
                    $daysRemaining = null;
                    if ($hasMembership && $status === 'active' && $m->expiry_date) {
                        $today = \Carbon\Carbon::today();
                        $expiry = \Carbon\Carbon::parse($m->expiry_date);
                        $daysRemaining = $today->diffInDays($expiry, false);
                        $daysRemaining = $daysRemaining < 0 ? 0 : $daysRemaining;
                    }
                    
                    // Status badge class
                    $statusBadgeClass = match($status) {
                        'active' => 'bg-success',
                        'expired' => 'bg-danger',
                        'pending' => 'bg-warning text-dark',
                        'cancelled' => 'bg-secondary',
                        default => 'bg-secondary'
                    };
                @endphp
                
                <div class="card bg-secondary bg-opacity-10 border border-secondary text-white">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-4 flex-wrap">

                            <!-- Avatar + Nama & Email -->
                            <div style="min-width: 200px;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="bg-warning text-dark rounded-circle d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                        style="width:36px;height:36px;font-size:.85rem;">
                                        {{ strtoupper(substr($m->member_name, 0, 1)) }}
                                    </span>
                                    <div>
                                        <div class="fw-bold small">{{ $m->member_name }}</div>
                                        <div class="text-white-50" style="font-size:.75rem;">{{ $m->email }}</div>
                                        <div class="text-white-50" style="font-size:.7rem;">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            Bergabung {{ \Carbon\Carbon::parse($m->joined_at)->translatedFormat('d M Y') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Gender -->
                            <div style="min-width: 50px;" class="text-center">
                                <i class="bi {{ $genderIcon }} fs-5 {{ $genderColor }}"></i>
                                <div class="text-white-50" style="font-size:.7rem;">{{ $m->gender }}</div>
                            </div>

                            <!-- Paket -->
                            <div style="min-width: 160px;">
                                @if ($hasMembership)
                                    <span class="badge bg-warning text-dark mb-1">{{ $m->package_name }}</span>
                                    <div class="small text-warning fw-bold">Rp {{ number_format($m->price, 0, ',', '.') }}</div>
                                    <div class="text-white-50" style="font-size:.7rem;">{{ $m->day_duration }} hari</div>
                                @else
                                    <span class="badge bg-secondary">Belum Daftar Paket</span>
                                    <div class="text-white-50 mt-1" style="font-size:.72rem;">
                                        <i class="bi bi-info-circle me-1"></i>Belum pernah membeli paket
                                    </div>
                                @endif
                            </div>

                            <!-- Tanggal Membership -->
                            <div class="flex-grow-1">
                                @if ($hasMembership)
                                    <div class="small text-white-50">Mulai:
                                        <span class="text-white">{{ \Carbon\Carbon::parse($m->start_date)->translatedFormat('d M Y') }}</span>
                                    </div>
                                    <div class="small text-white-50">Berakhir:
                                        <span class="text-white">{{ \Carbon\Carbon::parse($m->expiry_date)->translatedFormat('d M Y') }}</span>
                                    </div>
                                @else
                                    <div class="text-white-50 small">— Tidak ada data membership —</div>
                                @endif
                            </div>

                            <!-- Hari Tersisa -->
                            <div style="min-width: 80px;" class="text-center">
                                @if ($status === 'active' && $daysRemaining !== null)
                                    <div class="fs-5 fw-bold {{ $daysRemaining <= 7 ? 'text-danger' : 'text-success' }}">
                                        {{ $daysRemaining }}
                                    </div>
                                    <div class="small text-white-50">hari tersisa</div>
                                @else
                                    <span class="text-white-50 small">—</span>
                                @endif
                            </div>

                            <!-- Status -->
                            <div style="min-width: 100px;" class="text-center">
                                @if (!$hasMembership)
                                    <span class="badge bg-secondary">Tidak Ada</span>
                                @else
                                    <span class="badge {{ $statusBadgeClass }}">
                                        @switch($status)
                                            @case('active') Aktif @break
                                            @case('expired') Expired @break
                                            @case('pending') Pending @break
                                            @case('cancelled') Dibatalkan @break
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