@extends('layouts.member')

@section('title', 'Progress Member — Gymku')

@section('content')
<div class="container-fluid py-4">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-warning fw-bold mb-0">Progress Member</h4>
            <small class="text-white-50">Lihat semua progressmu disini</small>
        </div>
        <span class="text-white-50">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
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

    @if (!empty($rows) && count($rows) > 0)
        <!-- Grafik Progress -->
        <div class="card bg-secondary bg-opacity-10 border border-secondary text-white mb-3">
            <div class="card-body py-3">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                    <div class="fw-semibold text-warning">Grafik Progress</div>
                    
                    <div class="d-flex align-items-center gap-2">
                        <select id="chartMetric" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="weight">Weight (kg)</option>
                            <option value="height">Height (cm)</option>
                            <option value="body_fat">Body Fat (%)</option>
                            <option value="muscle_mass">Muscle Mass (kg)</option>
                            <option value="chest">Chest (cm)</option>
                            <option value="waist">Waist (cm)</option>
                            <option value="biceps">Biceps (cm)</option>
                            <option value="thigh">Thigh (cm)</option>
                        </select>

                        <select id="chartTime" class="form-select form-select-sm bg-dark text-light border-secondary">
                            <option value="week">Week</option>
                            <option value="month">Month</option>
                            <option value="quarter">Quarter</option>
                            <option value="year">Year</option>
                            <option value="all" selected>All time</option>
                        </select>
                    </div>
                </div>

                <div style="position: relative; height:300px; width:100%">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>
        </div>

        @if ($latest && $baseline)
            <!-- Perkembangan Progress -->
            <div class="card bg-secondary bg-opacity-10 border border-secondary text-white mb-3">
                <div class="card-body py-3">

                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                        <div>
                            <div class="fw-semibold text-warning">Perkembangan Progress</div>
                            <div class="small text-white-50">
                                <strong>{{ $baseline->record_date }}</strong>
                                &nbsp;→&nbsp;
                                <strong>{{ $latest->record_date }}</strong>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('member.progress') }}" class="m-0 d-flex align-items-center gap-2">
                            @csrf
                            <input type="hidden" name="action" value="set_compare_preset">
                            <div class="small text-white-50 d-none d-md-block">Bandingkan:</div>
                            <select name="preset" class="form-select form-select-sm bg-dark text-light border-secondary"
                                onchange="this.form.submit()">
                                <option value="week" {{ $preset === 'week' ? 'selected' : '' }}>Week</option>
                                <option value="month" {{ $preset === 'month' ? 'selected' : '' }}>Month</option>
                                <option value="quarter" {{ $preset === 'quarter' ? 'selected' : '' }}>Quarter</option>
                                <option value="year" {{ $preset === 'year' ? 'selected' : '' }}>Year</option>
                                <option value="all" {{ $preset === 'all' ? 'selected' : '' }}>All time</option>
                            </select>
                        </form>
                    </div>

                    @php
                        $metrics = [
                            ['key' => 'weight', 'label' => 'Weight', 'unit' => 'kg'],
                            ['key' => 'height', 'label' => 'Height', 'unit' => 'cm'],
                            ['key' => 'body_fat', 'label' => 'Body Fat', 'unit' => '%'],
                            ['key' => 'muscle_mass', 'label' => 'Muscle Mass', 'unit' => 'kg'],
                            ['key' => 'chest', 'label' => 'Chest', 'unit' => 'cm'],
                            ['key' => 'waist', 'label' => 'Waist', 'unit' => 'cm'],
                            ['key' => 'biceps', 'label' => 'Biceps', 'unit' => 'cm'],
                            ['key' => 'thigh', 'label' => 'Thigh', 'unit' => 'cm'],
                        ];
                    @endphp

                    <div class="row g-3">
                        @foreach ($metrics as $m)
                            @php
                                $newVal = $latest->{$m['key']};
                                $oldVal = $baseline->{$m['key']};
                                $delta = ($newVal && $oldVal) ? $newVal - $oldVal : null;
                                
                                if ($delta !== null) {
                                    $abs = abs($delta);
                                    $isInt = (abs($abs - round($abs)) < 0.00001);
                                    $num = $isInt ? (string)(int)round($abs) : number_format($abs, 2);
                                    
                                    if ($delta > 0) $sign = '+';
                                    elseif ($delta < 0) $sign = '-';
                                    else $sign = '';
                                    
                                    $text = $sign . $num . ' ' . $m['unit'];
                                    $textClass = $delta > 0 ? 'text-success' : ($delta < 0 ? 'text-danger' : 'text-light');
                                } else {
                                    $text = '-';
                                    $textClass = 'text-light';
                                }
                            @endphp

                            <div class="col-6 col-md-3">
                                <div class="text-center">
                                    <div class="fw-semibold mb-1">{{ $m['label'] }}</div>

                                    <div class="d-flex align-items-center justify-content-center rounded-circle border border-warning"
                                        style="width:92px;height:92px;margin:0 auto;border-width:2px !important;">
                                        <div class="fw-bold {{ $textClass }}">
                                            {{ $text }}
                                        </div>
                                    </div>

                                    <div class="small text-white-50 mt-1">
                                        {{ $oldVal ?? '-' }} → {{ $newVal ?? '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    @endif

    <!-- Tabel Progress -->
    <div class="card bg-secondary bg-opacity-10 border border-secondary text-white h-100">
        <div class="table-responsive">
            <table class="table table-hover table-dark align-middle mb-0">
                <thead>
                    <tr>
                        <th>Record Date</th>
                        <th>Weight (kg)</th>
                        <th>Height (cm)</th>
                        <th>Body Fat (%)</th>
                        <th>Muscle Mass (kg)</th>
                        <th>Chest (cm)</th>
                        <th>Waist (cm)</th>
                        <th>Biceps (cm)</th>
                        <th>Thigh (cm)</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    @if (empty($rows) || count($rows) == 0)
                        <tr>
                            <td colspan="10" class="text-center text-secondary py-4">
                                Belum ada data.
                            </td>
                        </tr>
                    @else
                        @php
                            $displayRows = $showAll ? $rows : array_slice($rows, 0, 1);
                        @endphp

                        @if (count($rows) > 0)
                            <tr>
                                <td colspan="10" class="text-white-50 small">
                                    Menampilkan {{ count($displayRows) }} dari {{ count($rows) }} data.
                                </td>
                            </tr>
                        @endif

                        @foreach ($displayRows as $r)
                            <tr>
                                <td>{{ $r->record_date }}</td>
                                <td>{{ $r->weight }}</td>
                                <td>{{ $r->height }}</td>
                                <td>{{ $r->body_fat }}</td>
                                <td>{{ $r->muscle_mass }}</td>
                                <td>{{ $r->chest }}</td>
                                <td>{{ $r->waist }}</td>
                                <td>{{ $r->biceps }}</td>
                                <td>{{ $r->thigh }}</td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-2">
                                        <button type="button" class="btn btn-outline-warning btn-sm fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $r->id_progress }}">
                                            Edit
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-sm fw-semibold"
                                            data-bs-toggle="modal" data-bs-target="#modalDelete{{ $r->id_progress }}">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="modalEdit{{ $r->id_progress }}" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light" style="border-radius:14px;">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title fw-bold">Edit Progress</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form method="POST" action="{{ route('member.progress.update', $r->id_progress) }}">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label">Record Date</label>
                                                        <input type="date" name="record_date" class="form-control form-control-sm"
                                                            value="{{ $r->record_date }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Weight (kg)</label>
                                                        <input type="number" step="0.01" name="weight" class="form-control form-control-sm"
                                                            value="{{ $r->weight }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Height (cm)</label>
                                                        <input type="number" step="0.01" name="height" class="form-control form-control-sm"
                                                            value="{{ $r->height }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Body Fat (%)</label>
                                                        <input type="number" step="0.01" name="body_fat" class="form-control form-control-sm"
                                                            value="{{ $r->body_fat }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Muscle Mass (kg)</label>
                                                        <input type="number" step="0.01" name="muscle_mass" class="form-control form-control-sm"
                                                            value="{{ $r->muscle_mass }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Chest (cm)</label>
                                                        <input type="number" step="0.01" name="chest" class="form-control form-control-sm"
                                                            value="{{ $r->chest }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Waist (cm)</label>
                                                        <input type="number" step="0.01" name="waist" class="form-control form-control-sm"
                                                            value="{{ $r->waist }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Biceps (cm)</label>
                                                        <input type="number" step="0.01" name="biceps" class="form-control form-control-sm"
                                                            value="{{ $r->biceps }}">
                                                    </div>

                                                    <div class="col-md-4">
                                                        <label class="form-label">Thigh (cm)</label>
                                                        <input type="number" step="0.01" name="thigh" class="form-control form-control-sm"
                                                            value="{{ $r->thigh }}">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning btn-sm fw-semibold">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Delete -->
                            <div class="modal fade" id="modalDelete{{ $r->id_progress }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content bg-dark text-light" style="border-radius:14px;">
                                        <div class="modal-header border-secondary">
                                            <h5 class="modal-title fw-bold text-danger">Hapus Progress</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <form method="POST" action="{{ route('member.progress.destroy', $r->id_progress) }}">
                                            @csrf
                                            @method('DELETE')

                                            <div class="modal-body">
                                                <p class="mb-2">Yakin mau hapus progress tanggal:</p>
                                                <div class="p-2 bg-secondary bg-opacity-25 rounded">
                                                    <strong>{{ $r->record_date }}</strong>
                                                </div>
                                                <small class="text-white-50 d-block mt-2">Aksi ini tidak bisa dibatalkan.</small>
                                            </div>

                                            <div class="modal-footer border-secondary">
                                                <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger btn-sm fw-semibold">Hapus</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex align-items-center mt-3">
        <button type="button" class="btn btn-warning btn-sm fw-semibold"
            data-bs-toggle="modal" data-bs-target="#modalAddProgress">
            + Tambah Progress
        </button>

        @if (!empty($rows) && count($rows) > 0)
            <form method="POST" action="{{ route('member.progress') }}" class="mx-4">
                @csrf
                <input type="hidden" name="action" value="toggle_show_all">
                <button type="submit" class="btn btn-outline-light btn-sm fw-semibold">
                    {{ $showAll ? 'Tampilkan Lebih Sedikit' : 'Tampilkan Lebih Banyak' }}
                </button>
            </form>
        @endif
    </div>

    <!-- Modal Add Progress -->
    <div class="modal fade" id="modalAddProgress" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-dark text-light" style="border-radius:14px;">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title fw-bold">Tambah Progress</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="{{ route('member.progress.store') }}">
                    @csrf

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Record Date</label>
                                <input type="date" name="record_date" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" step="0.01" name="height" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Body Fat (%)</label>
                                <input type="number" step="0.01" name="body_fat" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Muscle Mass (kg)</label>
                                <input type="number" step="0.01" name="muscle_mass" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Chest (cm)</label>
                                <input type="number" step="0.01" name="chest" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Waist (cm)</label>
                                <input type="number" step="0.01" name="waist" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Biceps (cm)</label>
                                <input type="number" step="0.01" name="biceps" class="form-control form-control-sm">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Thigh (cm)</label>
                                <input type="number" step="0.01" name="thigh" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-secondary">
                        <button type="button" class="btn btn-outline-light btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning btn-sm fw-semibold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawData = @json($rows ?? []);
    if (rawData.length === 0) return;

    const chartData = rawData.slice().reverse(); 
    const ctx = document.getElementById('progressChart').getContext('2d');
    const metricSelect = document.getElementById('chartMetric');
    const timeSelect = document.getElementById('chartTime');
    
    let progressChart;

    function getFilteredData(data, timeScale) {
        let filtered = [];

        if (timeScale === 'all') {
            filtered = data;
        } else {
            const latestDateStr = data[data.length - 1].record_date;
            const targetDate = new Date(latestDateStr);

            if (timeScale === 'week') targetDate.setDate(targetDate.getDate() - 7);
            else if (timeScale === 'month') targetDate.setMonth(targetDate.getMonth() - 1);
            else if (timeScale === 'quarter') targetDate.setMonth(targetDate.getMonth() - 3);
            else if (timeScale === 'year') targetDate.setFullYear(targetDate.getFullYear() - 1);

            filtered = data.filter(item => new Date(item.record_date) >= targetDate);
        }

        const MAX_POINTS = 12;
        
        if (filtered.length > MAX_POINTS) {
            const sampled = [];
            const step = (filtered.length - 1) / (MAX_POINTS - 1);

            for (let i = 0; i < MAX_POINTS; i++) {
                const index = Math.round(i * step);
                sampled.push(filtered[index]);
            }
            
            return sampled;
        }
        return filtered;
    }

    function updateChart() {
        const metric = metricSelect.value;
        const metricLabel = metricSelect.options[metricSelect.selectedIndex].text;
        const timeScale = timeSelect.value;

        const filteredData = getFilteredData(chartData, timeScale);

        const labels = filteredData.map(item => item.record_date);
        const dataPoints = filteredData.map(item => item[metric]);

        if (progressChart) {
            progressChart.destroy();
        }

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
                    pointBackgroundColor: '#212529',
                    pointBorderColor: '#ffc107',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        theme: 'dark'
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.7)' }
                    },
                    y: {
                        grid: { color: 'rgba(255, 255, 255, 0.1)' },
                        ticks: { color: 'rgba(255, 255, 255, 0.7)' },
                        beginAtZero: false 
                    }
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