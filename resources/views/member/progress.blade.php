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
                    @endphp

                    <div class="row g-3">
                        @foreach ($metrics as $m)
                            @php
                                $newVal = $latest->{$m['key']};
                                $oldVal = $baseline->{$m['key']};
                                $delta = ($newVal && $oldVal) ? $newVal - $oldVal : null;

                                $textClass = 'text-dark';
                                if ($delta > 0) {
                                    $textClass = 'text-success';
                                } elseif ($delta < 0) {
                                    $textClass = 'text-danger';
                                }

                                $formattedDelta = $delta !== null ? ($delta > 0 ? '+' : '') . number_format($delta, 1) . ' ' . $m['unit'] : '-';
                            @endphp

                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 rounded bg-light border border-light">
                                    <div class="mb-2">
                                        <i class="bi {{ $m['icon'] }} text-danger fs-5"></i>
                                    </div>
                                    <div class="small text-muted mb-2">{{ $m['label'] }}</div>
                                    <div class="h5 fw-bold {{ $textClass }} mb-1">{{ $formattedDelta }}</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">
                                        {{ $oldVal ? number_format($oldVal, 1) : '-' }}
                                        <i class="bi bi-chevron-right mx-1"></i>
                                        {{ $newVal ? number_format($newVal, 1) : '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
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
                        <tr class="border-light">
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($r->record_date)->format('d M Y') }}</div>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10">{{ number_format($r->weight, 1) }} kg</span>
                            </td>
                            <td>{{ $r->height ? number_format($r->height, 1) . ' cm' : '-' }}</td>
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
                            <td colspan="6" class="text-center py-5">
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
                        <label class="form-label text-muted">Berat Badan (kg) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" name="weight"
                               class="form-control bg-light border-0" required placeholder="Contoh: 70.5">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">Tinggi Badan (cm) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" name="height"
                            class="form-control bg-light border-0" required placeholder="Contoh: 170.5">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">Body Fat (%)</label>
                            <input type="number" step="0.1" name="body_fat"
                                   class="form-control bg-light border-0" placeholder="Opsional">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-muted">Muscle Mass (kg)</label>
                            <input type="number" step="0.1" name="muscle_mass"
                                   class="form-control bg-light border-0" placeholder="Opsional">
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
</script>
@endpush
