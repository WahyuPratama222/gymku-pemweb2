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

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- BAGIAN GRAFIK & STATISTIK (Hanya muncul jika ada data) --}}
    @if (!empty($allRows) && count($allRows) > 0)
        {{-- Grafik Progress --}}
        <div class="card bg-secondary bg-opacity-10 border border-secondary text-white mb-4 shadow-sm">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="fw-semibold text-warning">
                        <i class="bi bi-graph-up me-2"></i>Visualisasi Progress
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <select id="chartMetric" class="form-select form-select-sm bg-dark text-light border-secondary" style="min-width: 150px;">
                            <option value="weight">Berat Badan (kg)</option>
                            <option value="height">Tinggi Badan (cm)</option>
                            <option value="body_fat">Body Fat (%)</option>
                            <option value="muscle_mass">Muscle Mass (kg)</option>
                        </select>

                        <select id="chartTime" class="form-select form-select-sm bg-dark text-light border-secondary" style="min-width: 180px;">
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
                            <div class="fw-semibold text-warning">
                                <i class="bi bi-bar-chart-line me-2"></i>Perkembangan Tubuh
                            </div>
                            <div class="small text-white-50">
                                <strong>{{ \Carbon\Carbon::parse($baseline->record_date)->format('d M Y') }}</strong>
                                <i class="bi bi-arrow-right mx-2 text-warning"></i>
                                <strong>{{ \Carbon\Carbon::parse($latest->record_date)->format('d M Y') }}</strong>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <span class="small text-white-50">Bandingkan:</span>
                            <div class="btn-group" role="group">
                                <a href="{{ route('member.progress', ['preset' => 'week']) }}"
                                   class="btn btn-sm {{ $preset === 'week' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                                    Week
                                </a>
                                <a href="{{ route('member.progress', ['preset' => 'month']) }}"
                                   class="btn btn-sm {{ $preset === 'month' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
                                    Month
                                </a>
                                <a href="{{ route('member.progress', ['preset' => 'all']) }}"
                                   class="btn btn-sm {{ $preset === 'all' ? 'btn-warning text-dark' : 'btn-outline-warning' }}">
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

                                $textClass = 'text-light';
                                $bgClass = 'bg-secondary';
                                if ($delta > 0) {
                                    $textClass = 'text-success';
                                    $bgClass = 'bg-success';
                                } elseif ($delta < 0) {
                                    $textClass = 'text-danger';
                                    $bgClass = 'bg-danger';
                                }

                                $formattedDelta = $delta !== null ? ($delta > 0 ? '+' : '') . number_format($delta, 1) . ' ' . $m['unit'] : '-';
                            @endphp

                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 rounded bg-dark bg-opacity-25 border border-secondary border-opacity-50">
                                    <div class="mb-2">
                                        <i class="bi {{ $m['icon'] }} text-warning fs-5"></i>
                                    </div>
                                    <div class="small text-white-50 mb-2">{{ $m['label'] }}</div>
                                    <div class="h5 fw-bold {{ $textClass }} mb-1">{{ $formattedDelta }}</div>
                                    <div class="small text-white-50" style="font-size: 0.75rem;">
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
    <div class="card bg-dark border border-secondary text-white shadow-sm">
        <div class="card-header bg-secondary bg-opacity-10 border-bottom border-secondary">
            <h6 class="mb-0 text-white">
                <i class="bi bi-clock-history me-2"></i>Riwayat Progress
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-dark align-middle mb-0">
                <thead>
                    <tr>
                        <th class="text-warning">Tanggal</th>
                        <th class="text-warning">Berat (kg)</th>
                        <th class="text-warning">Tinggi (cm)</th>
                        <th class="text-warning">Body Fat (%)</th>
                        <th class="text-warning">Muscle Mass (kg)</th>
                        <th class="text-warning text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $displayRows = $showAll ? $allRows : $allRows->take(5);
                    @endphp

                    @forelse ($displayRows as $r)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ \Carbon\Carbon::parse($r->record_date)->format('d M Y') }}</div>
                            <td>
                                <span class="badge bg-warning text-dark">{{ number_format($r->weight, 1) }} kg</span>
                            </td>
                            <td>{{ $r->height ? number_format($r->height, 1) . ' cm' : '-' }}</td>
                            <td>{{ $r->body_fat ? number_format($r->body_fat, 1) . '%' : '-' }}</td>
                            <td>{{ $r->muscle_mass ? number_format($r->muscle_mass, 1) . ' kg' : '-' }}</td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDelete{{ $r->id_progress }}"
                                        title="Hapus data ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-white-50">
                                    <i class="bi bi-emoji-smile d-block mb-2" style="font-size: 2rem;"></i>
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
            <div class="card-footer text-center border-secondary bg-secondary bg-opacity-10">
                <a href="{{ route('member.progress', ['showAll' => !$showAll]) }}"
                   class="btn btn-link text-warning text-decoration-none">
                    {{ $showAll ? 'Tampilkan Lebih Sedikit' : 'Lihat Semua Riwayat (' . $allRows->count() . ')' }}
                </a>
            </div>
        @endif
    </div>
</div>

{{-- ========================================================================
     MODAL SECTION
     ======================================================================== --}}

{{-- MODAL CREATE --}}
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark text-white border-secondary">
            <form action="{{ route('member.progress.store') }}" method="POST">
                @csrf
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-warning">
                        <i class="bi bi-plus-circle me-2"></i>Input Progress Baru
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-white-50">
                            <i class="bi bi-calendar3 me-1"></i>Tanggal
                        </label>
                        <input type="date" name="record_date" value="{{ date('Y-m-d') }}"
                               class="form-control bg-dark text-white border-secondary" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50">
                            <i class="bi bi-speedometer2 me-1"></i>Berat Badan (kg) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.1" name="weight"
                               class="form-control bg-dark text-white border-secondary"
                               required placeholder="Contoh: 70.5">
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-white-50">
                            <i class="bi bi-arrows-vertical me-1"></i>Tinggi Badan (cm) <span class="text-danger">*</span>
                        </label>
                        <input type="number" step="0.1" name="height"
                            class="form-control bg-dark text-white border-secondary"
                            required placeholder="Contoh: 170.5">
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label text-white-50">
                                <i class="bi bi-droplet-fill me-1"></i>Body Fat (%)
                            </label>
                            <input type="number" step="0.1" name="body_fat"
                                   class="form-control bg-dark text-white border-secondary"
                                   placeholder="Opsional">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label text-white-50">
                                <i class="bi bi-lightning-fill me-1"></i>Muscle Mass (kg)
                            </label>
                            <input type="number" step="0.1" name="muscle_mass"
                                   class="form-control bg-dark text-white border-secondary"
                                   placeholder="Opsional">
                        </div>
                    </div>

                    <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 text-white-50 small">
                        <i class="bi bi-info-circle me-1"></i>
                        Field yang ditandai <span class="text-danger">*</span> wajib diisi.
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold px-4">
                        <i class="bi bi-save me-1"></i>Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL DELETE --}}
@foreach ($allRows as $r)
    <div class="modal fade" id="modalDelete{{ $r->id_progress }}" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content bg-dark text-white border-danger border-opacity-50">
                <form action="{{ route('member.progress.destroy', $r->id_progress) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3 text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                        <h6 class="text-white mb-2">Hapus Progress?</h6>
                        <p class="text-white-50 small mb-0">
                            Data tanggal <strong>{{ \Carbon\Carbon::parse($r->record_date)->format('d M Y') }}</strong> akan dihapus permanen.
                        </p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4">
                        <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="bi bi-trash me-1"></i>Ya, Hapus
                        </button>
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

    if (rawData.length === 0) {
        console.log('Tidak ada data untuk chart');
        return;
    }

    // Sort data by record_date ascending untuk chart
    const chartData = [...rawData].sort((a, b) => new Date(a.record_date) - new Date(b.record_date));

    const ctx = document.getElementById('progressChart');
    if (!ctx) {
        console.error('Canvas element not found');
        return;
    }

    const metricSelect = document.getElementById('chartMetric');
    const timeSelect = document.getElementById('chartTime');

    let progressChart;

    function updateChart() {
        const metric = metricSelect.value;
        const metricLabel = metricSelect.options[metricSelect.selectedIndex].text;
        const timeScale = timeSelect.value;

        let filteredData = chartData;

        // Filter berdasarkan time scale
        if (timeScale !== 'all') {
            const now = new Date();
            let cutoff = new Date();

            if (timeScale === 'week') {
                cutoff.setDate(now.getDate() - 7);
            } else if (timeScale === 'month') {
                cutoff.setMonth(now.getMonth() - 1);
            }

            filteredData = chartData.filter(item => new Date(item.record_date) >= cutoff);
        }

        // Prepare labels and data
        const labels = filteredData.map(item => {
            const d = new Date(item.record_date);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });

        const dataPoints = filteredData.map(item => parseFloat(item[metric]) || 0);

        // Destroy previous chart
        if (progressChart) {
            progressChart.destroy();
        }

        // Create new chart
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
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            color: 'rgba(255, 255, 255, 0.7)',
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: '#ffc107',
                        bodyColor: '#fff',
                        borderColor: '#ffc107',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        grid: {
                            color: 'rgba(255, 255, 255, 0.05)'
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                size: 11
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: 'rgba(255, 255, 255, 0.5)',
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });
    }

    // Initial chart render
    updateChart();

    // Update chart on select change
    metricSelect.addEventListener('change', updateChart);
    timeSelect.addEventListener('change', updateChart);
});
</script>
@endpush
