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
        <div class="alert alert-dismissible fade show border-0 shadow-sm" role="alert"
             style="background: linear-gradient(135deg, #fff3cd 0%, #fff8e7 100%); border-left: 4px solid #ffc107 !important; border-left-style: solid !important;">
            <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                    <span class="badge bg-warning text-dark p-2 rounded-3">
                        <i class="bi bi-lock-fill fs-5"></i>
                    </span>
                </div>
                <div>
                    <div class="fw-bold text-dark mb-1">
                        <i class="bi bi-star-fill text-warning me-1"></i>Fitur Premium Diperlukan
                    </div>
                    <div class="text-muted small">{{ session('error') }}</div>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (empty($packages) || count($packages) == 0)
        <div class="alert alert-light border text-muted">Belum ada paket tersedia saat ini.</div>
    @else
        <div class="row g-4">
            @foreach ($packages as $index => $pkg)
                @php 
                    // Tentukan apakah paket ini yang paling populer
                    $isFeatured = false;
                    
                    // Premium selalu featured
                    if ($pkg->is_premium) {
                        $isFeatured = true;
                    } 
                    // Non-premium: cek apakah ini yang paling banyak dibeli
                    elseif (!$pkg->is_premium && $pkg->id_package === $mostPopularNonPremiumId) {
                        $isFeatured = true;
                    }
                    
                    // Buat array benefit berdasarkan kategori
                    $benefits = [
                        "Akses gym selama {$pkg->day_duration} hari",
                        "Semua peralatan tersedia",
                        "Loker & ruang ganti",
                    ];
                    
                    // Tambahan benefit untuk premium
                    if ($pkg->is_premium) {
                        $benefits[] = "Konsultasi dengan trainer profesional";
                        $benefits[] = "Program latihan personal";
                        $benefits[] = "Analisis progress bulanan";
                        $benefits[] = "Akses kelas grup eksklusif";
                        $benefits[] = "Prioritas booking peralatan";
                    }

                    // Hitung harga per hari di awal
                    $pricePerDay = ceil($pkg->price / $pkg->day_duration);
                @endphp
                
                <div class="col-md-4">
                    <div class="card text-dark h-100 {{ $pkg->is_premium ? 'border-warning border-3 shadow-lg' : 'bg-white shadow-sm border-light' }}"
                         style="{{ $isFeatured && !$pkg->is_premium ? 'transform: translateY(-8px); transition: all 0.3s ease;' : '' }}">

                        @if ($pkg->is_premium)
                            <div class="text-center py-2 bg-gradient text-white rounded-top"
                                 style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); font-size:.75rem; font-weight:800; color: #000 !important;">
                                <i class="bi bi-star-fill me-1"></i>✨ PAKET PREMIUM ✨<i class="bi bi-star-fill ms-1"></i>
                            </div>
                        @elseif ($isFeatured && !$pkg->is_premium)
                            <div class="text-center py-1 bg-danger text-white rounded-top"
                                 style="font-size:.7rem; font-weight:800;">
                                ⭐ PALING POPULER
                            </div>
                        @endif

                        <div class="card-body d-flex flex-column {{ $pkg->is_premium ? 'bg-gradient' : '' }}"
                             style="{{ $pkg->is_premium ? 'background: linear-gradient(180deg, #fffbf0 0%, #ffffff 100%);' : '' }}">

                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h5 class="fw-bold mb-0 text-dark">{{ $pkg->name }}</h5>
                                <span class="badge {{ $pkg->is_premium ? 'bg-warning text-dark' : 'bg-danger' }}">
                                    {{ $pkg->day_duration }} Hari
                                </span>
                            </div>

                            @if ($pkg->is_premium)
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark border border-warning">
                                        <i class="bi bi-gem"></i> Premium Package
                                    </span>
                                </div>
                            @elseif ($isFeatured && !$pkg->is_premium)
                                <div class="mb-2">
                                    <span class="badge bg-danger text-white">
                                        <i class="bi bi-fire"></i> {{ $pkg->registrations_count }} Member Memilih
                                    </span>
                                </div>
                            @endif

                            <div class="mb-3">
                                <span class="text-muted small">Rp</span>
                                <span class="fs-2 fw-bold {{ $pkg->is_premium ? 'text-warning' : 'text-danger' }}">
                                    {{ number_format($pkg->price, 0, ',', '.') }}
                                </span>
                                <span class="text-muted small">/ {{ $pkg->day_duration }} hari</span>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    (Sekitar Rp {{ number_format($pricePerDay, 0, ',', '.') }} / hari)
                                </div>
                            </div>

                            <hr class="border-light">

                            <ul class="list-unstyled flex-grow-1 d-flex flex-column gap-2 mb-4">
                                @foreach ($benefits as $benefit)
                                    <li class="d-flex align-items-center gap-2 small">
                                        <i class="bi bi-check-circle-fill {{ $pkg->is_premium ? 'text-warning' : 'text-danger' }}"></i>
                                        {{ $benefit }}
                                    </li>
                                @endforeach
                            </ul>

                            <form action="{{ route('member.checkout') }}" method="GET">
                                <input type="hidden" name="id" value="{{ $pkg->id_package }}">

                                <div class="mb-3 p-3 rounded {{ $pkg->is_premium ? 'bg-warning bg-opacity-10 border border-warning border-opacity-25' : 'bg-light border border-light' }}">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6">
                                            <label class="form-label {{ $pkg->is_premium ? 'text-warning' : 'text-danger' }} small fw-bold mb-1">Durasi (Bulan)</label>
                                            <input type="number" name="quantity" class="form-control form-control-sm bg-white text-dark border-light shadow-sm"
                                                min="{{ $pkg->is_premium ? 1 : 0 }}" value="1" id="qty_{{ $pkg->id_package }}"
                                                oninput="updateTotalCombined({{ $pkg->id_package }}, {{ $pkg->price }}, {{ $pricePerDay }}, {{ $pkg->is_premium ? 'true' : 'false' }})">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label {{ $pkg->is_premium ? 'text-warning' : 'text-danger' }} small fw-bold mb-1">Tambah Hari</label>
                                            <input type="number" name="extra_days" class="form-control form-control-sm bg-white text-dark border-light shadow-sm"
                                                min="0" value="0" id="extra_{{ $pkg->id_package }}"
                                                oninput="updateTotalCombined({{ $pkg->id_package }}, {{ $pkg->price }}, {{ $pricePerDay }}, {{ $pkg->is_premium ? 'true' : 'false' }})">
                                        </div>
                                    </div>
                                    <div class="small fw-bold text-dark d-flex justify-content-between align-items-center">
                                        <span>Total Est:</span>
                                        <span id="total_display_{{ $pkg->id_package }}" class="{{ $pkg->is_premium ? 'text-warning' : 'text-danger' }} fs-6">Rp {{ number_format($pkg->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn fw-bold w-100 {{ $pkg->is_premium ? 'btn-warning text-dark shadow-lg' : ($isFeatured ? 'btn-danger shadow' : 'btn-outline-danger') }}">
                                    <i class="bi bi-cart-plus me-1"></i> {{ $pkg->is_premium ? 'Upgrade ke Premium' : 'Daftar Sekarang' }}
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
function updateTotalCombined(pkgId, basePrice, pricePerDay, isPremium) {
    let qtyInput = document.getElementById('qty_' + pkgId);
    let extraInput = document.getElementById('extra_' + pkgId);
    let display = document.getElementById('total_display_' + pkgId);

    let qty = parseInt(qtyInput.value) || 0;
    let extra = parseInt(extraInput.value) || 0;

    // Proteksi VIP di Frontend
    if (isPremium && qty < 1) {
        qty = 1;
        qtyInput.value = 1;
    }

    if (qty < 0) { qty = 0; qtyInput.value = 0; }
    if (extra < 0) { extra = 0; extraInput.value = 0; }

    let total = (basePrice * qty) + (extra * pricePerDay);
    display.innerText = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endpush