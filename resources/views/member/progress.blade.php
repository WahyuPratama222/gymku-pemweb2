@extends('layouts.member')

@section('title', 'Progress Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Progress Member</h4>
            <small class="text-white-50">Pantau transformasi fisikmu secara berkala</small>
        </div>
        {{-- TOMBOL TAMBAH: Sekarang di luar IF agar selalu bisa diklik walau data kosong --}}
        <button class="btn btn-warning shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#modalCreate">
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

    {{-- BAGIAN GRAFIK & STATISTIK (Hanya muncul jika ada data) --}}
    @if (!empty($rows) && count($rows) > 0)
        {{-- Grafik Progress --}}
        <div class="card bg-secondary bg-opacity-10 border border-secondary text-white mb-4 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="fw-semibold text-warning"><i class="bi bi-graph-up me-2"></i>Visualisasi Progress</div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <select id="chartMetric" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="weight">Weight (kg)</option>
                            <option value="body_fat">Body Fat (%)</option>
                            <option value="muscle_mass">Muscle Mass (kg)</option>
                        </select>

                        <select id="chartTime" class="form-select form-select-sm bg-dark text-light border-secondary">
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

        {{-- Widget Perkembangan (Baseline vs Latest) --}}
        @if (isset($latest) && isset($baseline))
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white mb-4 shadow-sm">
                <div class="card-body py-3">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
                        <div>
                            <div class="fw-semibold text-warning">Perkembangan Tubuh</div>
                            <div class="small text-white-50">
                                <strong>{{ \Carbon\Carbon::parse($baseline->record_date)->format('d/m/Y') }}</strong>
                                <i class="bi bi-arrow-right mx-2 text-warning"></i>
                                <strong>{{ \Carbon\Carbon::parse($latest->record_date)->format('d/m/Y') }}</strong>
                            </div>
                        </div>

                        {{-- Perhatikan route: member.progress --}}
                        <form method="POST" action="{{ route('member.progress') }}" class="m-0 d-flex align-items-center gap-2">
                            @csrf
                            <input type="hidden" name="action" value="set_compare_preset">
                            <select name="preset" class="form-select form-select-sm bg-dark text-light border-secondary" onchange="this.form.submit()">
                                <option value="week" {{ (isset($preset) && $preset === 'week') ? 'selected' : '' }}>Week</option>
                                <option value="month" {{ (isset($preset) && $preset === 'month') ? 'selected' : '' }}>Month</option>
                                <option value="all" {{ (isset($preset) && $preset === 'all') ? 'selected' : '' }}>All time</option>
                            </select>
                        </form>
                    </div>

                    @php
                        $metrics = [
                            ['key' => 'weight', 'label' => 'Weight', 'unit' => 'kg'],
                            ['key' => 'body_fat', 'label' => 'Body Fat', 'unit' => '%'],
                            ['key' => 'muscle_mass', 'label' => 'Muscle', 'unit' => 'kg'],
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach ($metrics as $m)
                            @php
                                $newVal = $latest->{$m['key']};
                                $oldVal = $baseline->{$m['key']};
                                $delta = ($newVal && $oldVal) ? $newVal - $oldVal : null;
                                
                                $textClass = 'text-light';
                                if ($delta > 0) $textClass = 'text-success';
                                elseif ($delta < 0) $textClass = 'text-danger';
                                
                                $formattedDelta = $delta !== null ? ($delta > 0 ? '+' : '') . round($delta, 2) . ' ' . $m['unit'] : '-';
                            @endphp

                            <div class="col-6 col-md-4">
                                <div class="text-center p-3 rounded bg-dark bg-opacity-25 border border-secondary border-opacity-50">
                                    <div class="small text-white-50 mb-2">{{ $m['label'] }}</div>
                                    <div class="h5 fw-bold {{ $textClass }} mb-1">{{ $formattedDelta }}</div>
                                    <div class="small text-white-50" style="font-size: 0.75rem;">
                                        {{ $oldVal ?? '-' }} <i class="bi bi-chevron-right mx-1"></i> {{ $newVal ?? '-' }}
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
    <div class="card bg-dark border border-secondary text-white shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover table-dark align-middle mb-0">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Berat</th>
                        <th>Body Fat</th>
                        <th>Muscle</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $displayRows = $showAll ? $rows : $rows->slice(0, 5);
                    @endphp

                    @forelse ($displayRows as $r)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($r->record_date)->format('d/m/Y') }}</td>
                            <td>{{ $r->weight }} kg</td>
                            <td>{{ $r->body_fat ?? '-' }}%</td>
                            <td>{{ $r->muscle_mass ?? '-' }} kg</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $r->id_progress }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDelete{{ $r->id_progress }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-white-50">
                                    <i class="bi bi-emoji-smile d-block mb-2" style="font-size: 2rem;"></i>
                                    Belum ada data. Klik tombol <strong>Tambah Progress</strong> untuk memulai!
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($rows->count() > 5)
            <div class="card-footer text-center border-secondary">
                <a href="{{ route('member.progress', ['showAll' => !$showAll]) }}" class="btn btn-link text-warning text-decoration-none">
                    {{ $showAll ? 'Tampilkan Sedikit' : 'Lihat Semua Riwayat' }}
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================================
     MODAL SECTION
     ======================================================================== --}}

{{-- MODAL CREATE: Penting agar user bisa input data pertama --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('member.progress.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning">Input Progres Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50">Tanggal</label>
                        <input type="date" name="record_date" value="{{ date('Y-m-d') }}" class="form-control bg-dark text-white border-secondary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-white-50">Berat Badan (kg)</label>
                        <input type="number" step="0.1" name="weight" class="form-control bg-dark text-white border-secondary" required placeholder="Contoh: 70.5">
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-white-50">Body Fat (%)</label>
                            <input type="number" step="0.1" name="body_fat" class="form-control bg-dark text-white border-secondary" placeholder="Opsional">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-white-50">Muscle Mass (kg)</label>
                            <input type="number" step="0.1" name="muscle_mass" class="form-control bg-dark text-white border-secondary" placeholder="Opsional">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning px-4">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach ($rows as $r)
    {{-- MODAL EDIT --}}
    <div class="modal fade" id="modalEdit{{ $r->id_progress }}" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-dark text-white border-secondary">
                <form action="{{ route('member.progress.update', $r->id_progress) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title">Edit Data: {{ \Carbon\Carbon::parse($r->record_date)->format('d/m/Y') }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-white-50">Berat Badan (kg)</label>
                            <input type="number" step="0.1" name="weight" value="{{ $r->weight }}" class="form-control bg-dark text-white border-secondary" required>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label text-white-50">Body Fat (%)</label>
                                <input type="number" step="0.1" name="body_fat" value="{{ $r->body_fat }}" class="form-control bg-dark text-white border-secondary">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label text-white-50">Muscle Mass (kg)</label>
                                <input type="number" step="0.1" name="muscle_mass" value="{{ $r->muscle_mass }}" class="form-control bg-dark text-white border-secondary">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- MODAL DELETE --}}
    <div class="modal fade" id="modalDelete{{ $r->id_progress }}" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-danger border-opacity-50">
                <form action="{{ route('member.progress.destroy', $r->id_progress) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3 text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <p class="mb-0">Hapus progress tanggal <strong>{{ \Carbon\Carbon::parse($r->record_date)->format('d/m/Y') }}</strong>?</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger">Ya, Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawData = @json($rows ?? []);
    if (rawData.length === 0) return;

    const chartData = [...rawData].sort((a, b) => new Date(a.record_date) - new Date(b.record_date));
    const ctx = document.getElementById('progressChart').getContext('2d');
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
        const dataPoints = filteredData.map(item => item[metric]);

        if (progressChart) progressChart.destroy();

        progressChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: metricLabel,
                    data: dataPoints,
                    borderColor: '#ffc107',
                    backgroundColor: 'rgba(255, 193, 7, 0.1)',
                    borderWidth: 3,
                    pointBackgroundColor: '#ffc107',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: 'rgba(255, 255, 255, 0.5)' } },
                    x: { grid: { display: false }, ticks: { color: 'rgba(255, 255, 255, 0.5)' } }
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