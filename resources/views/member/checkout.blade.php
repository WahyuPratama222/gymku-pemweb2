@extends('layouts.member')

@section('title', 'Checkout — Gymku')

@section('content')
<div class="container py-5">

    <!-- Header -->
    <div class="text-center mb-5">
        <h2 class="fw-bold text-white">Selesaikan <span class="text-warning">Pembayaran</span></h2>
        <p class="text-white-50">Langkah terakhir untuk mengaktifkan paket membership Anda.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 justify-content-center">

        <!-- Kiri: Form Metode Pembayaran -->
        <div class="col-lg-7">
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-white">
                        <i class="bi bi-credit-card me-2 text-warning"></i>Pilih Metode Pembayaran
                    </h5>

                    <form method="POST" action="{{ route('member.checkout.process') }}" id="formCheckout">
                        @csrf
                        <input type="hidden" name="id_package" value="{{ $package->id_package }}">
                        <input type="hidden" name="extra_days" value="{{ $extraDays }}">

                        <!-- Tanggal Mulai -->
                        <div class="mb-4">
                            <label class="form-label text-warning small fw-semibold">Tanggal Mulai</label>
                            <input type="date" name="start_date"
                                   class="form-control bg-dark text-white border-secondary"
                                   value="{{ date('Y-m-d') }}"
                                   min="{{ date('Y-m-d') }}" required>
                        </div>

                        <label class="form-label text-warning small fw-semibold mb-3">Metode Pembayaran</label>

                        <div class="row g-3">

                            <!-- Transfer Bank -->
                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="Transfer Bank" id="pm_bank" required>
                                <label class="btn btn-outline-warning w-100 text-start py-3 px-3" for="pm_bank">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-bank2 fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">Transfer Bank</div>
                                            <div class="small opacity-75">BCA, Mandiri, BNI, BRI</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- Tunai -->
                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="Tunai" id="pm_cash" required>
                                <label class="btn btn-outline-warning w-100 text-start py-3 px-3" for="pm_cash">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-cash-coin fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">Tunai / Cash</div>
                                            <div class="small opacity-75">Bayar di resepsionis</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- QRIS -->
                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="QRIS" id="pm_qris" required>
                                <label class="btn btn-outline-warning w-100 text-start py-3 px-3" for="pm_qris">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-qr-code fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">QRIS</div>
                                            <div class="small opacity-75">Scan dengan aplikasi apa saja</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <!-- E-Wallet -->
                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="E-Wallet" id="pm_ewallet" required>
                                <label class="btn btn-outline-warning w-100 text-start py-3 px-3" for="pm_ewallet">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-phone fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">E-Wallet</div>
                                            <div class="small opacity-75">OVO, ShopeePay, LinkAja</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                        </div><!-- end row methods -->

                        <div class="mt-4 p-3 rounded-3 border border-secondary bg-dark bg-opacity-50">
                            <p class="mb-0 small text-white-50">
                                <i class="bi bi-info-circle text-warning me-1"></i>
                                Status pembayaran awal <strong class="text-warning">Belum Lunas</strong>.
                                Admin akan mengonfirmasi pembayaranmu segera.
                            </p>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kanan: Ringkasan Pesanan -->
        <div class="col-lg-4">
            <div class="card border border-warning border-2 text-white h-100"
                 style="background: linear-gradient(180deg, #232a31, #20262d);">
                <div class="card-body p-4 d-flex flex-column">

                    <h5 class="fw-bold text-white mb-1">{{ $package->name }}</h5>
                    <span class="badge bg-warning text-dark mb-3" style="width:fit-content;">
                        {{ $totalDays }} Hari {{ $extraDays > 0 ? '(+'.$extraDays.' Hari)' : '' }}
                    </span>

                    <!-- Harga -->
                    <div class="mb-3">
                        <span class="text-white-50 small">Rp</span>
                        <span class="fs-2 fw-bold text-white">
                            {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>
                        <span class="text-white-50 small">/ {{ $totalDays }} hari</span>
                    </div>

                    <hr class="border-secondary">

                    <!-- Fitur -->
                    <ul class="list-unstyled flex-grow-1 d-flex flex-column gap-2 mb-4">
                        <li class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-check-circle-fill text-warning"></i>
                            Akses gym selama {{ $totalDays }} hari
                        </li>
                        <li class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-check-circle-fill text-warning"></i>
                            Semua peralatan tersedia
                        </li>
                        <li class="d-flex align-items-center gap-2 small">
                            <i class="bi bi-check-circle-fill text-warning"></i>
                            Loker & ruang ganti
                        </li>
                        @if ($package->day_duration >= 60)
                            <li class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Konsultasi dengan trainer
                            </li>
                        @endif
                        @if ($package->day_duration >= 90)
                            <li class="d-flex align-items-center gap-2 small">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Program latihan personal
                            </li>
                        @endif
                    </ul>

                    <!-- Tombol -->
                    <button type="submit" form="formCheckout"
                            class="btn btn-warning text-dark fw-bold w-100 mb-2">
                        <i class="bi bi-lock-fill me-1"></i> Bayar Sekarang
                    </button>
                    <a href="{{ route('member.packages') }}"
                       class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection