@extends('layouts.member')

@section('title', 'Riwayat Pembayaran — Gymku')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Riwayat Pembayaran</h4>
            <small class="text-muted">Daftar semua transaksi yang Anda lakukan</small>
        </div>
    </div>

    {{-- ALERT MESSAGES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABLE CARD --}}
    <div class="card bg-white border-0 shadow-sm text-dark h-100">
        <div class="card-body">

            @if (empty($payments) || count($payments) == 0)
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2 text-danger"></i>
                    <p class="mb-0">Belum ada riwayat pembayaran.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table mb-0 align-middle text-nowrap">
                        <thead class="border-bottom border-light">
                            <tr>
                                <th class="py-3 text-danger fw-bold">Tanggal & Waktu</th>
                                <th class="py-3 text-danger fw-bold">Paket Langganan</th>
                                <th class="py-3 text-center text-danger fw-bold">Metode</th>
                                <th class="py-3 text-end text-danger fw-bold">Total Bayar</th>
                                <th class="py-3 text-center text-danger fw-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $p)
                                <tr class="border-bottom border-light">
                                    <td class="py-3">
                                        <div class="fw-medium text-dark small">
                                            {{ \Carbon\Carbon::parse($p->payment_date)->translatedFormat('d M Y') }}
                                        </div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            <i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($p->payment_date)->format('H:i') }} WIB
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="fw-bold text-dark small">{{ $p->package_name }}</div>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            Aktif s/d <span class="text-danger fw-medium">{{ \Carbon\Carbon::parse($p->expiry_date)->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="small text-muted">
                                            <i class="bi bi-wallet2 me-1"></i>{{ $p->payment_method }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end">
                                        <span class="fw-bold text-danger small">Rp {{ number_format($p->amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if ($p->payment_status === 'Lunas')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2">
                                                <i class="bi bi-check-circle me-1"></i>Lunas
                                            </span>
                                        @elseif ($p->payment_status === 'Belum Lunas')
                                            {{-- Menjaga warna default status pending agar tetap informatif --}}
                                            <span class="badge bg-warning bg-opacity-10 text-dark border border-warning border-opacity-25 px-3 py-2">
                                                <i class="bi bi-hourglass-split me-1"></i>Pending
                                            </span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                                                <i class="bi bi-x-circle me-1"></i>Gagal
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <span class="text-muted small">Menampilkan <span class="text-danger fw-bold">{{ count($payments) }}</span> transaksi terakhir</span>
                </div>
            @endif

        </div>
    </div>

</div>
@endsection
