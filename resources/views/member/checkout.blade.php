@extends('layouts.member')

@section('title', 'Checkout — Gymku')

@section('content')
<div class="container py-5">

    <div class="text-center mb-5">
        <h2 class="fw-bold text-dark">Selesaikan <span class="text-danger">Pembayaran</span></h2>
        <p class="text-muted">Langkah terakhir untuk mengaktifkan paket membership Anda.</p>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4 justify-content-center">

        <div class="col-lg-7">
            <div class="card bg-white border-0 shadow-sm text-dark h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4 text-dark">
                        <i class="bi bi-credit-card me-2 text-danger"></i>Pilih Metode Pembayaran
                    </h5>

                    <form method="POST" action="{{ route('member.checkout.process') }}" id="formCheckout">
                        @csrf
                        <input type="hidden" name="id_package" value="{{ $package->id_package }}">

                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label class="form-label text-danger small fw-semibold">Tanggal Mulai</label>
                                <input type="date" name="start_date" id="start_date"
                                       class="form-control bg-light border-light text-dark"
                                       value="{{ date('Y-m-d') }}"
                                       min="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-danger small fw-semibold">Durasi (Bulan)</label>
                                <input type="number" name="quantity" id="quantity"
                                       class="form-control bg-light border-light text-dark"
                                       value="{{ $quantity }}"
                                       min="0" required
                                       oninput="updateSummary()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-danger small fw-semibold">Tambah Hari</label>
                                <input type="number" name="extra_days" id="extra_days"
                                       class="form-control bg-light border-light text-dark"
                                       value="{{ $extraDays }}"
                                       min="0" required
                                       oninput="updateSummary()">
                            </div>
                        </div>

                        <label class="form-label text-danger small fw-semibold mb-3">Metode Pembayaran</label>

                        <div class="row g-3">

                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="Transfer Bank" id="pm_bank" required>
                                <label class="btn btn-outline-danger w-100 text-start py-3 px-3 border-light shadow-sm" for="pm_bank">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-bank2 fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">Transfer Bank</div>
                                            <div class="small opacity-75">BCA, Mandiri, BNI, BRI</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="Tunai" id="pm_cash" required>
                                <label class="btn btn-outline-danger w-100 text-start py-3 px-3 border-light shadow-sm" for="pm_cash">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-cash-coin fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">Tunai / Cash</div>
                                            <div class="small opacity-75">Bayar di resepsionis</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="QRIS" id="pm_qris" required>
                                <label class="btn btn-outline-danger w-100 text-start py-3 px-3 border-light shadow-sm" for="pm_qris">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-qr-code fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">QRIS</div>
                                            <div class="small opacity-75">Scan dengan aplikasi apa saja</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="col-sm-6">
                                <input type="radio" class="btn-check" name="payment_method"
                                       value="E-Wallet" id="pm_ewallet" required>
                                <label class="btn btn-outline-danger w-100 text-start py-3 px-3 border-light shadow-sm" for="pm_ewallet">
                                    <div class="d-flex align-items-center gap-3">
                                        <i class="bi bi-phone fs-5"></i>
                                        <div>
                                            <div class="fw-semibold">E-Wallet</div>
                                            <div class="small opacity-75">OVO, ShopeePay, LinkAja</div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                        </div>

                        <div class="mt-4 p-3 rounded-3 border border-light bg-light">
                            <p class="mb-0 small text-muted">
                                <i class="bi bi-info-circle text-danger me-1"></i>
                                Status pembayaran awal <strong class="text-danger">Belum Lunas</strong>.
                                Admin akan mengonfirmasi pembayaranmu segera.
                            </p>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow text-dark h-100 {{ $package->is_premium ? 'bg-gradient border-warning border-2' : 'bg-white' }}"
                 style="{{ $package->is_premium ? 'background: linear-gradient(180deg, #fffbf0 0%, #ffffff 100%);' : '' }}">
                <div class="card-body p-4 d-flex flex-column">

                    @if ($package->is_premium)
                        <div class="text-center mb-3 p-2 rounded" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);">
                            <span class="fw-bold text-dark"><i class="bi bi-gem"></i> PAKET PREMIUM</span>
                        </div>
                    @endif

                    <h5 class="fw-bold text-dark mb-1">{{ $package->name }}</h5>
                    <span class="badge {{ $package->is_premium ? 'bg-warning text-dark' : 'bg-danger text-white' }} mb-3" style="width:fit-content;" id="badge_duration">
                        {{ $totalDays }} Hari
                    </span>

                    <div class="mb-3">
                        <span class="text-muted small">Rp</span>
                        <span class="fs-2 fw-bold {{ $package->is_premium ? 'text-warning' : 'text-danger' }}" id="display_price">
                            {{ number_format($totalPrice, 0, ',', '.') }}
                        </span>
                        <span class="text-muted small">/ <span id="display_days">{{ $totalDays }}</span> hari</span>
                    </div>

                    <hr class="border-light">

                    <ul class="list-unstyled flex-grow-1 d-flex flex-column gap-2 mb-4" id="benefit_list">
                        <li class="d-flex align-items-center gap-2 small text-dark">
                            <i class="bi bi-check-circle-fill {{ $package->is_premium ? 'text-warning' : 'text-danger' }}"></i>
                            Akses gym selama <span class="summary_days">{{ $totalDays }}</span> hari
                        </li>
                        <li class="d-flex align-items-center gap-2 small text-dark">
                            <i class="bi bi-check-circle-fill {{ $package->is_premium ? 'text-warning' : 'text-danger' }}"></i>
                            Semua peralatan tersedia
                        </li>
                        <li class="d-flex align-items-center gap-2 small text-dark">
                            <i class="bi bi-check-circle-fill {{ $package->is_premium ? 'text-warning' : 'text-danger' }}"></i>
                            Loker & ruang ganti
                        </li>
                        @if ($package->is_premium)
                            <li class="d-flex align-items-center gap-2 small text-dark">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Konsultasi dengan trainer profesional
                            </li>
                            <li class="d-flex align-items-center gap-2 small text-dark">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Program latihan personal
                            </li>
                            <li class="d-flex align-items-center gap-2 small text-dark">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Analisis progress bulanan
                            </li>
                            <li class="d-flex align-items-center gap-2 small text-dark">
                                <i class="bi bi-check-circle-fill text-warning"></i>
                                Akses kelas grup eksklusif
                            </li>
                        @endif
                    </ul>

                    <button type="submit" form="formCheckout"
                            class="btn {{ $package->is_premium ? 'btn-warning text-dark' : 'btn-danger text-white' }} fw-bold w-100 mb-2 shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> Bayar Sekarang
                    </button>
                    <a href="{{ route('member.packages') }}"
                       class="btn btn-light w-100 text-muted">
                        <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
                    </a>

                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function updateSummary() {
        const quantityInput = document.getElementById('quantity');
        const extraInput = document.getElementById('extra_days');
        
        let quantity = parseInt(quantityInput.value) || 0;
        let extra = parseInt(extraInput.value) || 0;

        // Proteksi VIP di Checkout
        const isPremium = {{ $package->is_premium ? 'true' : 'false' }};
        if (isPremium && quantity < 1) {
            quantity = 1;
            quantityInput.value = 1;
        }

        if (quantity < 0) { quantity = 0; quantityInput.value = 0; }
        if (extra < 0) { extra = 0; extraInput.value = 0; }

        const basePrice = {{ $package->price }};
        const baseDays = {{ $package->day_duration }};
        const pricePerDay = {{ $pricePerDay }};
        
        const totalPrice = (basePrice * quantity) + (extra * pricePerDay);
        const totalDays = (baseDays * quantity) + extra;

        // Update displays
        document.getElementById('display_price').innerText = totalPrice.toLocaleString('id-ID');
        document.getElementById('display_days').innerText = totalDays;
        document.getElementById('badge_duration').innerText = totalDays + ' Hari';
        
        const summaryDaysElements = document.getElementsByClassName('summary_days');
        for (let el of summaryDaysElements) {
            el.innerText = totalDays;
        }
    }
</script>
@endpush
@endsection