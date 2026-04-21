@extends('layouts.auth')

@section('title', 'Daftar Akun — Gymku')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <!-- Logo -->
            <div class="text-center mb-4">
                <i class="bi bi-trophy-fill text-warning" style="font-size: 2.5rem;"></i>
                <h4 class="text-white fw-bold mt-2">Buat Akun <span class="text-warning">Gymku</span></h4>
                <p class="text-white-50 small">Mulai perjalanan fitnesmu hari ini!</p>
            </div>

            <!-- Card Form -->
            <div class="card bg-secondary bg-opacity-10 border border-secondary shadow">
                <div class="card-body p-4">

                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show py-2 small" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            @foreach ($errors->all() as $error)
                                {{ $error }}<br>
                            @endforeach
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Nama -->
                        <div class="mb-3">
                            <label class="form-label text-warning small">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-secondary"
                                placeholder="Nama kamu" value="{{ old('name') }}" required autofocus>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label class="form-label text-warning small">Email</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary"
                                placeholder="contoh@email.com" value="{{ old('email') }}" required>
                        </div>

                        <!-- Jenis Kelamin -->
                        <div class="mb-3">
                            <label class="form-label text-warning small">Jenis Kelamin</label>
                            <select name="gender" class="form-select bg-dark text-white border-secondary" required>
                                <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Pilih Jenis Kelamin</option>
                                <option value="Laki-Laki" {{ old('gender') == 'Laki-Laki' ? 'selected' : '' }}>Laki-Laki</option>
                                <option value="Wanita" {{ old('gender') == 'Wanita' ? 'selected' : '' }}>Wanita</option>
                            </select>
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label class="form-label text-warning small">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control bg-dark text-white border-secondary"
                                    placeholder="Min. 8 karakter" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                    onclick="togglePass('passwordInput', 'eyeIcon1')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon1"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Konfirmasi Password -->
                        <div class="mb-4">
                            <label class="form-label text-warning small">Konfirmasi Password</label>
                            <div class="input-group">
                                <input type="password" name="password_confirmation" id="confirmInput"
                                    class="form-control bg-dark text-white border-secondary"
                                    placeholder="Ulangi password" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                    onclick="togglePass('confirmInput', 'eyeIcon2')" tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon2"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning fw-bold w-100 text-dark">
                            Buat Akun
                        </button>

                    </form>
                </div>
            </div>

            <p class="text-center text-white-50 small mt-3">
                Sudah punya akun?
                <a href="{{ route('login') }}" class="text-warning text-decoration-none">Login di sini</a>
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