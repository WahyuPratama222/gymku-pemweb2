@extends('layouts.admin')

@section('title', 'Manajemen Paket — Gymku')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Kelola Paket</h4>
            <small class="text-muted">Kelola paket membership gym</small>
        </div>
        <button class="btn btn-danger fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahPaket">
            <i class="bi bi-plus-lg me-1"></i> Tambah Paket
        </button>
    </div>

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

    @if (empty($packages) || count($packages) == 0)
        <div class="alert alert-light border text-dark">Belum ada paket tersedia.</div>
    @else
        <div class="row g-4">
            @foreach ($packages as $p)
                <div class="col-sm-6 col-xl-4 col-xxl-3">
                    <div class="card bg-white border-0 shadow-sm text-dark h-100 hover-lift" style="transition: transform 0.3s ease;">
                        <div class="card-body p-4 d-flex flex-column gap-3">

                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold fs-5 text-dark mb-1">{{ $p->name }}</div>
                                    <div class="small text-muted mb-2">
                                        <i class="bi bi-calendar-range me-1"></i>{{ $p->day_duration }} Hari
                                    </div>
                                    @if ($p->is_premium)
                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-star-fill"></i> Premium
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">Reguler</span>
                                    @endif
                                </div>
                                @if ($p->status === 'Active')
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">Aktif</span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">Nonaktif</span>
                                @endif
                            </div>

                            <div class="border-top border-bottom py-3 my-2">
                                <div class="text-danger fw-bold fs-3">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                                <div class="small text-muted">per {{ $p->day_duration }} hari</div>
                            </div>

                            <div class="d-flex gap-2 mt-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id_package }}">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary"
                                    data-bs-toggle="modal" data-bs-target="#modalDelete{{ $p->id_package }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit{{ $p->id_package }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-white border-0 text-dark shadow">
                            <div class="modal-header border-light">
                                <h5 class="modal-title fw-bold">Edit Paket</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form method="POST" action="{{ route('admin.packages.update', $p->id_package) }}">
                                @csrf
                                @method('PUT')
                                <div class="modal-body">
                                    <div class="mb-3">
                                        <label class="form-label text-danger small">Nama Paket</label>
                                        <input type="text" name="name" class="form-control bg-light border-light"
                                            value="{{ $p->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-danger small">Harga (Rp)</label>
                                        <input type="number" name="price" class="form-control bg-light border-light"
                                            value="{{ $p->price }}" min="0" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-danger small">Durasi (Hari)</label>
                                        <input type="number" name="day_duration"
                                            class="form-control bg-light border-light"
                                            value="{{ $p->day_duration }}" min="1" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-danger small">Kategori Paket</label>
                                        <select name="is_premium" class="form-select bg-light border-light">
                                            <option value="0" {{ !$p->is_premium ? 'selected' : '' }}>Reguler</option>
                                            <option value="1" {{ $p->is_premium ? 'selected' : '' }}>Premium</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-danger small">Status</label>
                                        <select name="status" class="form-select bg-light border-light">
                                            <option value="Active" {{ $p->status === 'Active' ? 'selected' : '' }}>Aktif</option>
                                            <option value="Inactive" {{ $p->status === 'Inactive' ? 'selected' : '' }}>Nonaktif</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-light">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-danger fw-bold">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Delete -->
                <div class="modal fade" id="modalDelete{{ $p->id_package }}" tabindex="-1">
                    <div class="modal-dialog modal-sm modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <form action="{{ route('admin.packages.destroy', $p->id_package) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body text-center py-4">
                                    <i class="bi bi-trash3 text-danger mb-3 d-block" style="font-size: 2.5rem;"></i>
                                    <h6 class="text-dark fw-bold mb-2">Hapus Paket?</h6>
                                    <p class="text-muted small mb-0">
                                        Paket <strong>{{ $p->name }}</strong> akan dihapus permanen.
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
        </div>
    @endif

    <!-- Modal Tambah Paket -->
    <div class="modal fade" id="modalTambahPaket" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-white border-0 text-dark shadow">
                <div class="modal-header border-light">
                    <h5 class="modal-title fw-bold">Tambah Paket Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="{{ route('admin.packages.store') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label text-danger small">Nama Paket</label>
                            <input type="text" name="name" class="form-control bg-light border-light"
                                placeholder="Contoh: Paket Basic" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger small">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control bg-light border-light"
                                placeholder="150000" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger small">Durasi (Hari)</label>
                            <input type="number" name="day_duration"
                                class="form-control bg-light border-light" placeholder="30" min="1"
                                required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger small">Kategori Paket</label>
                            <select name="is_premium" class="form-select bg-light border-light">
                                <option value="0">Reguler</option>
                                <option value="1">Premium</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-danger small">Status</label>
                            <select name="status" class="form-select bg-light border-light">
                                <option value="Active">Aktif</option>
                                <option value="Inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger fw-bold">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>

@push('styles')
<style>
.hover-lift:hover {
    transform: translateY(-4px);
}
</style>
@endpush

@endsection