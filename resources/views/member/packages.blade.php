@extends('layouts.member')

@section('title', 'Paket Gym — Gymku')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Paket Gym</h4>
            <small class="text-white-50">Pilih paket yang sesuai dan mulai latihan sekarang</small>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (empty($packages) || count($packages) == 0)
        <div class="alert alert-warning text-dark-50">Belum ada paket tersedia saat ini.</div>
    @else
        <div class="row g-4">
            @foreach ($packages as $index => $pkg)
                @php $isFeatured = $index === 1; @endphp
                <div class="col-md-4">
                    <div class="card text-white h-100 bg-secondary bg-opacity-10
                                {{ $isFeatured ? 'border border-warning border-2 shadow-lg' : 'border border-secondary' }}"
                         style="{{ $isFeatured ? 'transform: translateY(-6px);' : '' }}">

                        @if ($isFeatured)
                            <div class="text-center py-1 bg-warning rounded-top"
                                 style="font-size:.7rem; font-weight:800; color:#111;">
                                ⭐ PALING POPULER
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">

                            <!-- Nama + Badge Durasi -->
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold mb-0">{{ $pkg->name }}</h5>
                                <span class="badge bg-warning text-dark">
                                    {{ $pkg->day_duration }} Hari
                                </span>
                            </div>

                            <!-- Harga -->
                            <div class="mb-3">
                                <span class="text-white-50 small">Rp</span>
                                <span class="fs-2 fw-bold">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <span class="text-white-50 small">/ {{ $pkg->day_duration }} hari</span>
                            </div>

                            <hr class="border-secondary">

                            <!-- Fitur -->
                            <ul class="list-unstyled flex-grow-1 d-flex flex-column gap-2 mb-4">
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    Akses gym selama {{ $pkg->day_duration }} hari
                                </li>
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    Semua peralatan tersedia
                                </li>
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-warning"></i>
                                    Loker & ruang ganti
                                </li>
                                @if ($pkg->day_duration >= 60)
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-warning"></i>
                                        Konsultasi dengan trainer
                                    </li>
                                @endif
                                @if ($pkg->day_duration >= 90)
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-warning"></i>
                                        Program latihan personal
                                    </li>
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-warning"></i>
                                        Analisis progress bulanan
                                    </li>
                                @endif
                            </ul>

                            <!-- Opsi Tambah Hari & Tombol Daftar -->
                            <form action="{{ route('member.checkout') }}" method="GET">
                                <input type="hidden" name="id" value="{{ $pkg->id_package }}">
                                
                                @php $pricePerDay = ceil($pkg->price / $pkg->day_duration); @endphp
                                <div class="mb-3 p-3 rounded bg-dark border border-secondary shadow-sm">
                                    <label class="form-label text-warning small fw-bold mb-1">Tambah Hari (Opsional)</label>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <input type="number" name="extra_days" class="form-control form-control-sm bg-secondary bg-opacity-25 text-white border-secondary" 
                                            min="0" value="0" style="width: 70px;" 
                                            oninput="updateTotal(this, {{ $pkg->price }}, {{ $pricePerDay }}, 'total_display_{{ $pkg->id_package }}')">
                                        <small class="text-white-50">+ Rp {{ number_format($pricePerDay, 0, ',', '.') }} /hari</small>
                                    </div>
                                    <div class="small fw-bold text-white d-flex justify-content-between align-items-center">
                                        <span>Total:</span>
                                        <span id="total_display_{{ $pkg->id_package }}" class="text-info fs-6">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn fw-bold w-100 {{ $isFeatured ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                                    <i class="bi bi-cart-plus me-1"></i> Daftar Sekarang
                                </button>
                            </form>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function updateTotal(input, basePrice, pricePerDay, displayId) {
    let days = parseInt(input.value) || 0;
    if (days < 0) {
        days = 0;
        input.value = 0;
    }
    let total = basePrice + (days * pricePerDay);
    document.getElementById(displayId).innerText = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endpush