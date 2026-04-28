@extends('layouts.auth')

@section('title', 'Login — Gymku')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">

            <div class="text-center mb-4">
                <i class="bi bi-trophy-fill text-danger" style="font-size: 2.5rem;"></i>
                <h4 class="text-dark fw-bold mt-2">Masuk ke <span class="text-danger">Gymku</span></h4>
                <p class="text-muted small">Selamat datang kembali!</p>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show py-2 small" role="alert">
                    <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

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

                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label text-danger small">Email</label>
                            <input type="email" name="email" class="form-control bg-light text-dark border-light"
                                placeholder="contoh@email.com" value="{{ old('email') }}" required autofocus>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-danger small">Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="passwordInput"
                                    class="form-control bg-light text-dark border-light" placeholder="••••••••"
                                    required>
                                <button type="button" class="btn btn-outline-secondary border-light bg-light" onclick="togglePassword()"
                                    tabindex="-1">
                                    <i class="bi bi-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-danger fw-bold w-100">
                            Masuk
                        </button>

                    </form>

                </div>
            </div>

            <p class="text-center text-muted small mt-3">
                Belum punya akun?
                <a href="{{ route('register') }}" class="text-danger text-decoration-none fw-bold">Daftar sekarang</a>
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
