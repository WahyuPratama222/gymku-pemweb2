@extends('layouts.auth')

@section('title', 'Daftar Akun — Gymku')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="text-center mb-4">
                <i class="bi bi-trophy-fill text-danger" style="font-size: 2.5rem;"></i>
                <h4 class="text-dark fw-bold mt-2">Buat Akun <span class="text-danger">Gymku</span></h4>
                <p class="text-muted small">Mulai perjalanan fitnesmu hari ini!</p>
            </div>

            <div class="card bg-white border-light shadow-sm">
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-danger small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control bg-light text-dark border-light"
                                placeholder="Nama kamu" value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-danger small">Email</label>
                            <input type="email" name="email" class="form-control bg-light text-dark border-light"
                                placeholder="contoh@email.com" value="{{ old('email') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-danger small">Jenis Kelamin</label>
                            <select name="gender" class="form-select bg-light text-dark border-light" required>
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                                <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Wanita" {{ old('gender') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-danger small">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control bg-light text-dark border-light"
                                    placeholder="Min. 8 karakter" required>
                                <button type="button" class="btn btn-outline-secondary border-light bg-light"
                                    onclick="togglePass('passwordInput', 'eyeIcon1')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-danger small">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmInput"
                                    class="form-control bg-light text-dark border-light"
                                    placeholder="Ulangi password" required>
                                <button type="button" class="btn btn-outline-secondary border-light bg-light"
                                    onclick="togglePass('confirmInput', 'eyeIcon2')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger fw-bold w-100">
                            Buat Akun
                        </button>

                    </form>
                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-danger text-decoration-none fw-bold">Login di sini</a>
            </p>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePass(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>
@endpush
