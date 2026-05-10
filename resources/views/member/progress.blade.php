@extends('layouts.member')

@section('title', 'Progress Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Progress Member</h4>
            <small class="text-muted">Pantau transformasi fisikmu secara berkala</small>
        </div>
        <button class="btn btn-danger shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreate">
            <i class="bi bi-plus-lg me-2"></i>Tambah Progress
        </button>
    </div>

    {{-- Alert Messages --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->has('general'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first('general') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BAGIAN GRAFIK & STATISTIK --}}
    @if (!empty($allRows) && count($allRows) > 0)
        {{-- Grafik Progress --}}
        <div class="card bg-white border-0 text-dark mb-4 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="fw-semibold text-danger">
                        <i class="bi bi-graph-up me-2"></i>Visualisasi Progress
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <select id="chartMetric" class="form-select form-select-sm bg-light text-dark border-light" style="min-width: 150px;">
                            <option value="weight">Berat Badan (kg)</option>
                            <option value="height">Tinggi Badan (cm)</option>
                            <option value="body_fat">Body Fat (%)</option>
                            <option value="muscle_mass">Muscle Mass (kg)</option>
                        </select>

                        <select id="chartTime" class="form-select form-select-sm bg-light text-dark border-light" style="min-width: 180px;">
                            <option value="week">1 Minggu Terakhir</option>
                            <option value="month">1 Bulan Terakhir</option>
                            <option value="all" selected>Semua Waktu</option>
                        </select>
                    </div>
                </div>

                <div style="position: relative; height:320px; width:100%">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Widget Perkembangan --}}
        @if (isset($latest) && isset($baseline))
            <div class="card bg-white border-0 text-dark mb-4 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="fw-semibold text-danger">
                                <i class="bi bi-bar-chart-line me-2"></i>Perkembangan Tubuh
                            </div>
                            <div class="small text-muted">
                                <strong>{{ \Carbon\Carbon::parse($baseline->record_date)->format('d M Y') }}</strong>
                                <i class="bi bi-arrow-right mx-2 text-danger"></i>
                                <strong>{{ \Carbon\Carbon::parse($latest->record_date)->format('d M Y') }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-muted">Bandingkan:</span>
                            <div class="btn-group" role="group">
                                <a href="{{ route('member.progress', ['preset' => 'week']) }}"
                                   class="btn btn-sm {{ $preset === 'week' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    Week
                                </a>
                                <a href="{{ route('member.progress', ['preset' => 'month']) }}"
                                   class="btn btn-sm {{ $preset === 'month' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    Month
                                </a>
                                <a href="{{ route('member.progress', ['preset' => 'all']) }}"
                                   class="btn btn-sm {{ $preset === 'all' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    All Time
                                </a>
                            </div>
                        </div>
                    </div>

                    @php
                        $metrics = [
                            ['key' => 'weight', 'label' => 'Berat Badan', 'unit' => 'kg', 'icon' => 'bi-speedometer2'],
                            ['key' => 'height', 'label' => 'Tinggi Badan', 'unit' => 'cm', 'icon' => 'bi-arrows-vertical'],
                            ['key' => 'body_fat', 'label' => 'Body Fat', 'unit' => '%', 'icon' => 'bi-droplet-fill'],
                            ['key' => 'muscle_mass', 'label' => 'Muscle Mass', 'unit' => 'kg', 'icon' => 'bi-lightning-fill'],
                        ];

                        // Hitung BMI latest & baseline
                        $bmiLatest   = ($latest->weight && $latest->height)
                            ? $latest->weight / (($latest->height / 100) ** 2) : null;
                        $bmiBaseline = ($baseline->weight && $baseline->height)
                            ? $baseline->weight / (($baseline->height / 100) ** 2) : null;

                        $bmiCategoryFn = function($bmi) {
                            if ($bmi === null) return ['label' => '-', 'class' => 'text-muted'];
                            if ($bmi < 18.5) return ['label' => 'Kurus', 'class' => 'text-info'];
                            if ($bmi < 25.0) return ['label' => 'Normal', 'class' => 'text-success'];
                            if ($bmi < 30.0) return ['label' => 'Gemuk', 'class' => 'text-warning'];
                            return ['label' => 'Obesitas', 'class' => 'text-danger'];
                        };

                        $bmiDelta    = ($bmiLatest && $bmiBaseline) ? $bmiLatest - $bmiBaseline : null;
                        $bmiCatNew   = $bmiCategoryFn($bmiLatest);
                        $bmiTextClass = 'text-dark';
                        if ($bmiDelta !== null) {
                            $bmiTextClass = $bmiDelta > 0 ? 'text-success' : ($bmiDelta < 0 ? 'text-danger' : 'text-dark');
                        }
                    @endphp

                    @php
                        // Helper untuk hitung delta & textClass
                        $card = function($key, $unit) use ($latest, $baseline) {
                            $newVal = $latest->{$key};
                            $oldVal = $baseline->{$key};
                            $delta  = ($newVal && $oldVal) ? $newVal - $oldVal : null;
                            $textClass = 'text-dark';
                            if ($delta > 0) $textClass = 'text-success';
                            elseif ($delta < 0) $textClass = 'text-danger';
                            return [
                                'newVal'    => $newVal,
                                'oldVal'    => $oldVal,
                                'delta'     => $delta,
                                'textClass' => $textClass,
                                'formatted' => $delta !== null ? ($delta > 0 ? '+' : '') . number_format($delta, 1) . ' ' . $unit : '-',
                            ];
                        };

                        $cWeight = $card('weight', 'kg');
                        $cHeight = $card('height', 'cm');
                        $cBf     = $card('body_fat', '%');
                        $cMm     = $card('muscle_mass', 'kg');
                    @endphp

                    <div class="row g-3 row-cols-2 row-cols-md-5">

                        {{-- 1. Berat Badan --}}
                        <div class="col">
                            <div class="text-center p-3 rounded bg-light border border-light h-100">
                                <div class="mb-2"><i class="bi bi-speedometer2 text-danger fs-5"></i></div>
                                <div class="small text-muted mb-2">Berat Badan</div>
                                <div class="h5 fw-bold {{ $cWeight['textClass'] }} mb-1">{{ $cWeight['formatted'] }}</div>
                                <div class="small text-muted" style="font-size:0.75rem;">
                                    {{ $cWeight['oldVal'] ? number_format($cWeight['oldVal'], 1) : '-' }}
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    {{ $cWeight['newVal'] ? number_format($cWeight['newVal'], 1) : '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- 2. Tinggi Badan --}}
                        <div class="col">
                            <div class="text-center p-3 rounded bg-light border border-light h-100">
                                <div class="mb-2"><i class="bi bi-arrows-vertical text-danger fs-5"></i></div>
                                <div class="small text-muted mb-2">Tinggi Badan</div>
                                <div class="h5 fw-bold {{ $cHeight['textClass'] }} mb-1">{{ $cHeight['formatted'] }}</div>
                                <div class="small text-muted" style="font-size:0.75rem;">
                                    {{ $cHeight['oldVal'] ? number_format($cHeight['oldVal'], 1) : '-' }}
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    {{ $cHeight['newVal'] ? number_format($cHeight['newVal'], 1) : '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- 3. BMI (tengah) --}}
                        <div class="col">
                            <div class="text-center p-3 rounded border h-100"
                                 style="background: linear-gradient(135deg, #fff5f5, #fff);">
                                <div class="mb-2"><i class="bi bi-calculator text-danger fs-5"></i></div>
                                <div class="small text-muted mb-2 fw-semibold">BMI</div>
                                <div class="h5 fw-bold {{ $bmiCatNew['class'] }} mb-1">
                                    {{ $bmiLatest ? number_format($bmiLatest, 1) : '-' }}
                                </div>
                                <div class="small {{ $bmiCatNew['class'] }} fw-semibold" style="font-size:0.8rem;">
                                    {{ $bmiCatNew['label'] }}
                                </div>
                            </div>
                        </div>

                        {{-- 4. Body Fat --}}
                        <div class="col">
                            <div class="text-center p-3 rounded bg-light border border-light h-100">
                                <div class="mb-2"><i class="bi bi-droplet-fill text-danger fs-5"></i></div>
                                <div class="small text-muted mb-2">Body Fat</div>
                                <div class="h5 fw-bold {{ $cBf['textClass'] }} mb-1">{{ $cBf['formatted'] }}</div>
                                <div class="small text-muted" style="font-size:0.75rem;">
                                    {{ $cBf['oldVal'] ? number_format($cBf['oldVal'], 1) : '-' }}
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    {{ $cBf['newVal'] ? number_format($cBf['newVal'], 1) : '-' }}
                                </div>
                            </div>
                        </div>

                        {{-- 5. Muscle Mass --}}
                        <div class="col">
                            <div class="text-center p-3 rounded bg-light border border-light h-100">
                                <div class="mb-2"><i class="bi bi-lightning-fill text-danger fs-5"></i></div>
                                <div class="small text-muted mb-2">Muscle Mass</div>
                                <div class="h5 fw-bold {{ $cMm['textClass'] }} mb-1">{{ $cMm['formatted'] }}</div>
                                <div class="small text-muted" style="font-size:0.75rem;">
                                    {{ $cMm['oldVal'] ? number_format($cMm['oldVal'], 1) : '-' }}
                                    <i class="bi bi-chevron-right mx-1"></i>
                                    {{ $cMm['newVal'] ? number_format($cMm['newVal'], 1) : '-' }}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- TABEL RIWAYAT --}}
    <div class="card bg-white border-0 text-dark shadow-sm">
        <div class="card-header bg-light border-bottom-0">
            <h6 class="mb-0 text-dark fw-bold">
                <i class="bi bi-clock-history me-2"></i>Riwayat Progress
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-danger fw-bold border-light">Tanggal</th>
                        <th class="text-danger fw-bold border-light">Berat (kg)</th>
                        <th class="text-danger fw-bold border-light">Tinggi (cm)</th>
                        <th class="text-danger fw-bold border-light">BMI</th>
                        <th class="text-danger fw-bold border-light">Body Fat (%)</th>
                        <th class="text-danger fw-bold border-light">Muscle Mass (kg)</th>
                        <th class="text-danger fw-bold text-end border-light">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $displayRows = $showAll ? $allRows : $allRows->take(5);
                    @endphp

                    @forelse ($displayRows as $r)
                        @php
                            $bmi = ($r->weight && $r->height)
                                ? $r->weight / (($r->height / 100) ** 2)
                                : null;
                            $bmiCat = $bmi === null ? null : ($bmi < 18.5 ? ['label'=>'Kurus','bg'=>'bg-info'] : ($bmi < 25 ? ['label'=>'Normal','bg'=>'bg-success'] : ($bmi < 30 ? ['label'=>'Gemuk','bg'=>'bg-warning'] : ['label'=>'Obesitas','bg'=>'bg-danger'])));
                        @endphp
                        <tr class="border-light">
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($r->record_date)->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">{{ number_format($r->weight, 1) }} kg</span>
                            </td>
                            <td>{{ $r->height ? number_format($r->height, 1) . ' cm' : '-' }}</td>
                            <td>
                                @if ($bmi)
                                    <span class="badge {{ $bmiCat['bg'] }} text-white">
                                        {{ number_format($bmi, 1) }} — {{ $bmiCat['label'] }}
                                    </span>
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $r->body_fat ? number_format($r->body_fat, 1) . '%' : '-' }}</td>
                            <td>{{ $r->muscle_mass ? number_format($r->muscle_mass, 1) . ' kg' : '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger border-0"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDelete{{ $r->id_progress }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                        <td colspan="7" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-emoji-smile d-block mb-2 text-danger" style="font-size: 2rem;"></i>
                                    <p class="mb-0">Belum ada data progress.</p>
                                    <p class="small">Klik tombol <strong>Tambah Progress</strong> untuk memulai!</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allRows->count() > 5)
            <div class="card-footer text-center border-0 bg-light">
                <a href="{{ route('member.progress', ['showAll' => !$showAll]) }}"
                   class="btn btn-link text-danger text-decoration-none fw-bold">
                    {{ $showAll ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua Riwayat (' . $allRows->count() . ')' }}
                </a>
            </div>
        @endif
    </div>
</div>

{{-- MODAL CREATE --}}
@php
    $isFirstTime = empty($latest);
@endphp
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('member.progress.store') }}" method="POST">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title text-danger fw-bold">
                        <i class="bi bi-plus-circle me-2"></i>Input Progress Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">

                    <div class="mb-3">
                        <label class="form-label text-muted">Tanggal</label>
                        <input type="date" name="record_date" value="{{ date('Y-m-d') }}"
                               class="form-control bg-light border-0" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">
                            Berat Badan (kg)
                            @if ($isFirstTime)<span class="text-danger">*</span>@endif
                        </label>
                        <input type="number" step="0.1" name="weight" id="modalWeight"
                               class="form-control bg-light border-0"
                               {{ $isFirstTime ? 'required' : '' }}
                               placeholder="{{ $isFirstTime ? 'Contoh: 70.5' : number_format($latest->weight, 1) }}"
                               value="{{ old('weight') }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">
                            Tinggi Badan (cm)
                            @if ($isFirstTime)<span class="text-danger">*</span>@endif
                        </label>
                        <input type="number" step="0.1" name="height" id="modalHeight"
                               class="form-control bg-light border-0"
                               {{ $isFirstTime ? 'required' : '' }}
                               placeholder="{{ $isFirstTime ? 'Contoh: 170.5' : number_format($latest->height, 1) }}"
                               value="{{ old('height') }}">
                    </div>

                    {{-- Panel BMI Real-time --}}
                    <div id="bmiPanel" class="d-none mb-3 p-3 rounded bg-light border border-light">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-calculator text-danger"></i>
                                <span class="small text-muted fw-semibold">BMI Saat Ini</span>
                            </div>
                            <div class="text-end">
                                <span id="bmiValue" class="fw-bold fs-5"></span>
                                <span id="bmiCategory" class="badge ms-2"></span>
                            </div>
                        </div>
                        <div id="bmiBar" class="mt-2" style="height:6px; border-radius:3px; background:#e9ecef; position:relative;">
                            <div id="bmiIndicator" style="position:absolute; top:-3px; width:12px; height:12px; border-radius:50%; background:#dc3545; transform:translateX(-50%);"></div>
                        </div>
                        <div class="d-flex justify-content-between mt-1" style="font-size:0.65rem; color:#aaa;">
                            <span>Kurus &lt;18.5</span>
                            <span>Normal 18.5–25</span>
                            <span>Gemuk 25–30</span>
                            <span>Obesitas &gt;30</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">
                                Body Fat (%)
                                @if ($isFirstTime)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="number" step="0.1" name="body_fat"
                                   class="form-control bg-light border-0"
                                   {{ $isFirstTime ? 'required' : '' }}
                                   placeholder="{{ $isFirstTime ? 'Contoh: 15.0' : ($latest->body_fat ? number_format($latest->body_fat, 1) : 'Contoh: 15.0') }}"
                                   value="{{ old('body_fat') }}">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">
                                Muscle Mass (kg)
                                @if ($isFirstTime)<span class="text-danger">*</span>@endif
                            </label>
                            <input type="number" step="0.1" name="muscle_mass"
                                   class="form-control bg-light border-0"
                                   {{ $isFirstTime ? 'required' : '' }}
                                   placeholder="{{ $isFirstTime ? 'Contoh: 35.0' : ($latest->muscle_mass ? number_format($latest->muscle_mass, 1) : 'Contoh: 35.0') }}"
                                   value="{{ old('muscle_mass') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger fw-bold px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DELETE --}}
@foreach ($allRows as $r)
    <div class="modal fade" id="modalDelete{{ $r->id_progress }}" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <form action="{{ route('member.progress.destroy', $r->id_progress) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3 text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h6 class="text-dark fw-bold mb-2">Hapus Progress?</h6>
                        <p class="text-muted small mb-0">
                            Data tanggal <strong>{{ \Carbon\Carbon::parse($r->record_date)->format('d M Y') }}</strong> akan dihapus permanen.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger px-3">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawData = @json($allRows ?? []);
    if (rawData.length === 0) return;

    const chartData = [...rawData].sort((a, b) => new Date(a.record_date) - new Date(b.record_date));
    const ctx = document.getElementById('progressChart');
    if (!ctx) return;

    const metricSelect = document.getElementById('chartMetric');
    const timeSelect = document.getElementById('chartTime');
    let progressChart;

    function updateChart() {
        const metric = metricSelect.value;
        const metricLabel = metricSelect.options[metricSelect.selectedIndex].text;
        const timeScale = timeSelect.value;
        let filteredData = chartData;

        if (timeScale !== 'all') {
            const now = new Date();
            let cutoff = new Date();
            if (timeScale === 'week') cutoff.setDate(now.getDate() - 7);
            else if (timeScale === 'month') cutoff.setMonth(now.getMonth() - 1);
            filteredData = chartData.filter(item => new Date(item.record_date) >= cutoff);
        }

        const labels = filteredData.map(item => {
            const d = new Date(item.record_date);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        const dataPoints = filteredData.map(item => parseFloat(item[metric]) || 0);

        if (progressChart) progressChart.destroy();

        progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: metricLabel,
                    data: dataPoints,
                    borderColor: '#dc3545', // Red
                    backgroundColor: 'rgba(220, 53, 69, 0.05)',
                    borderWidth: 3,
                    pointBackgroundColor: '#dc3545',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { labels: { color: '#6c757d' } }
                },
                scales: {
                    y: { grid: { color: '#f8f9fa' }, ticks: { color: '#6c757d' } },
                    x: { grid: { display: false }, ticks: { color: '#6c757d' } }
                }
            }
        });
    }

    updateChart();
    metricSelect.addEventListener('change', updateChart);
    timeSelect.addEventListener('change', updateChart);
});

// ── Kalkulator BMI Real-time di Modal ──────────────────────────────────────
(function () {
    const weightInput = document.getElementById('modalWeight');
    const heightInput = document.getElementById('modalHeight');
    const bmiPanel    = document.getElementById('bmiPanel');
    const bmiValue    = document.getElementById('bmiValue');
    const bmiCategory = document.getElementById('bmiCategory');
    const bmiIndicator = document.getElementById('bmiIndicator');

    if (!weightInput || !heightInput) return;

    function calcBMI() {
        const w = parseFloat(weightInput.value);
        const h = parseFloat(heightInput.value);

        if (!w || !h || h <= 0) {
            bmiPanel.classList.add('d-none');
            return;
        }

        const bmi = w / ((h / 100) ** 2);
        bmiPanel.classList.remove('d-none');
        bmiValue.textContent = bmi.toFixed(1);

        // Kategori & warna
        let label, color, badgeClass;
        if (bmi < 18.5) {
            label = 'Kurus'; color = '#0dcaf0'; badgeClass = 'bg-info text-dark';
        } else if (bmi < 25) {
            label = 'Normal'; color = '#198754'; badgeClass = 'bg-success';
        } else if (bmi < 30) {
            label = 'Gemuk'; color = '#ffc107'; badgeClass = 'bg-warning text-dark';
        } else {
            label = 'Obesitas'; color = '#dc3545'; badgeClass = 'bg-danger';
        }

        bmiCategory.textContent = label;
        bmiCategory.className = 'badge ms-2 ' + badgeClass;
        bmiValue.style.color = color;
        bmiIndicator.style.background = color;

        // Posisi indikator pada bar (range BMI 10–40 → 0–100%)
        const pct = Math.min(Math.max((bmi - 10) / 30 * 100, 0), 100);
        bmiIndicator.style.left = pct + '%';
    }

    weightInput.addEventListener('input', calcBMI);
    heightInput.addEventListener('input', calcBMI);

    // Hitung sekali jika ada nilai lama (old input)
    calcBMI();
})();
</script>
@endpush
