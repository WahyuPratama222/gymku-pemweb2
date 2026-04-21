@extends('layouts.auth')

@section('title', 'Login — Gymku')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <!-- Logo -->
            <div class="text-center mb-4">
                <i class="bi bi-trophy-fill text-warning" style="font-size: 2.5rem;"></i>
                <h4 class="text-white fw-bold mt-2">Masuk ke <span class="text-warning">Gymku</span></h4>
                <p class="text-white-50 small">Selamat datang kembali!</p>
            </div>

            <!-- Flash message (sukses register, dll.) -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-warning small">Email</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-secondary"
                                placeholder="contoh@email.com" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-warning small">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control bg-dark text-white border-secondary" placeholder="••••••••"
                                    required>
                                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()"
                                    tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning fw-bold w-100 text-dark">
                            Masuk
                        </button>

                    </form>

                </div>
            </div>

            <p class="text-center text-white-50 small mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-warning text-decoration-none">Daftar sekarang</a>
            </p>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('eyeIcon');
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