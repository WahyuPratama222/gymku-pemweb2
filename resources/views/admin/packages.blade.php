@extends('layouts.admin')

@section('title', 'Manajemen Paket — Gymku')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="text-danger fw-bold mb-0">Kelola Paket</h4>
            <small class="text-muted">Kelola paket membership gym</small>
        </div>
        <button class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#modalTambahPaket">
            <i class="bi bi-plus-lg me-1"></i> Tambah Paket
        </button>
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
        <div class="alert alert-light border text-dark">Belum ada paket tersedia.</div>
    @else
        <div class="row g-3">
            @foreach ($packages as $p)
                <div class="col-sm-6 col-xl-4">
                    <div class="card bg-white border-0 shadow-sm text-dark h-100">
                        <div class="card-body p-4 d-flex flex-column gap-3">

                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold fs-6">{{ $p->name }}</div>
                                    <div class="small text-muted">{{ $p->day_duration }} Hari</div>
                                    @if ($p->is_premium)
                                        <span class="badge bg-warning text-dark mt-1">
                                            <i class="bi bi-star-fill"></i> Premium
                                        </span>
                                    @else
                                        <span class="badge bg-secondary mt-1">Reguler</span>
                                    @endif
                                </div>
                                @if ($p->status === 'Active')
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </div>

                            <div class="text-danger fw-bold fs-5">Rp {{ number_format($p->price, 0, ',', '.') }}</div>

                            <div class="d-flex gap-2 mt-auto">
                                <button type="button" class="btn btn-sm btn-outline-danger flex-grow-1"
                                    data-bs-toggle="modal" data-bs-target="#modalEdit{{ $p->id_package }}">
                                    <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <form action="{{ route('admin.packages.destroy', $p->id_package) }}" method="POST"
                                    onsubmit="return confirm('Hapus paket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div class="modal fade" id="modalEdit{{ $p->id_package }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content bg-white border-0 text-dark">
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
            @endforeach
        </div>
    @endif

    <!-- Modal Tambah Paket -->
    <div class="modal fade" id="modalTambahPaket" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content bg-white border-0 text-dark">
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
@endsection