@extends('layouts.member')

@section('title', 'Paket Gym — Gymku')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Paket Gym</h4>
            <small class="text-muted">Pilih paket yang sesuai dan mulai latihan sekarang</small>
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
        <div class="alert alert-light border text-muted">Belum ada paket tersedia saat ini.</div>
    @else
        <div class="row g-4">
            @foreach ($packages as $index => $pkg)
                @php $isFeatured = $index === 1; @endphp
                <div class="col-md-4">
                    <div class="card text-dark h-100 bg-white shadow-sm
                                {{ $isFeatured ? 'border-danger border-2' : 'border-light' }}"
                         style="{{ $isFeatured ? 'transform: translateY(-6px); transition: all 0.3s ease;' : '' }}">

                        @if ($isFeatured)
                            <div class="text-center py-1 bg-danger text-white rounded-top"
                                 style="font-size:.7rem; font-weight:800;">
                                ⭐ PALING POPULER
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold mb-0 text-dark">{{ $pkg->name }}</h5>
                                <span class="badge bg-danger">
                                    {{ $pkg->day_duration }} Hari
                                </span>
                            </div>

                            <div class="mb-3">
                                <span class="text-muted small">Rp</span>
                                <span class="fs-2 fw-bold text-danger">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <span class="text-muted small">/ {{ $pkg->day_duration }} hari</span>
                            </div>

                            <hr class="border-light">

                            <ul class="list-unstyled flex-grow-1 d-flex flex-column gap-2 mb-4">
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-danger"></i>
                                    Akses gym selama {{ $pkg->day_duration }} hari
                                </li>
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-danger"></i>
                                    Semua peralatan tersedia
                                </li>
                                <li class="d-flex align-items-center gap-2 small">
                                    <i class="bi bi-check-circle-fill text-danger"></i>
                                    Loker & ruang ganti
                                </li>
                                @if ($pkg->day_duration >= 60)
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-danger"></i>
                                        Konsultasi dengan trainer
                                    </li>
                                @endif
                                @if ($pkg->day_duration >= 90)
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-danger"></i>
                                        Program latihan personal
                                    </li>
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill text-danger"></i>
                                        Analisis progress bulanan
                                    </li>
                                @endif
                            </ul>

                            <form action="{{ route('member.checkout') }}" method="GET">
                                <input type="hidden" name="id" value="{{ $pkg->id_package }}">

                                @php $pricePerDay = ceil($pkg->price / $pkg->day_duration); @endphp
                                <div class="mb-3 p-3 rounded bg-light border border-light">
                                    <label class="form-label text-danger small fw-bold mb-1">Tambah Hari (Opsional)</label>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <input type="number" name="extra_days" class="form-control form-control-sm bg-white text-dark border-light shadow-sm"
                                            min="0" value="0" style="width: 70px;"
                                            oninput="updateTotal(this, {{ $pkg->price }}, {{ $pricePerDay }}, 'total_display_{{ $pkg->id_package }}')">
                                        <small class="text-muted">+ Rp {{ number_format($pricePerDay, 0, ',', '.') }} /hari</small>
                                    </div>
                                    <div class="small fw-bold text-dark d-flex justify-content-between align-items-center">
                                        <span>Total:</span>
                                        <span id="total_display_{{ $pkg->id_package }}" class="text-danger fs-6">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                <button type="submit" class="btn fw-bold w-100 {{ $isFeatured ? 'btn-danger shadow' : 'btn-outline-danger' }}">
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
