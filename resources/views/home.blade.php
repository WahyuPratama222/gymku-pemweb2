@extends('layouts.app')

@section('title', 'Beranda — Gymku')

@section('content')
<div class="bg-white py-5">
    <div class="container">
        <div class="row align-items-center g-4 mb-5">
            <div class="col-md-7 text-dark">
                <h1 class="display-4 fw-bold text-dark">Selamat Datang di <span class="text-danger">Gymku</span></h1>
                <p class="lead opacity-75 mt-3">
                    Gymku adalah gym yang menyediakan alat standar internasional untuk mendukung latihan beban dan
                    cardio Anda.
                </p>
            </div>

            <div class="col-md-4 d-flex justify-content-end">
                <div class="position-relative d-inline-block p-4 text-center">
                    <i class="bi bi-trophy-fill text-danger display-1 mb-3 d-block"></i>
                    <div class="mt-2">
                        <h4 class="text-dark fw-bold mb-1">Palembang <span class="text-danger">#1</span></h4>
                        <p class="text-muted small">Premium Gym Experience</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 justify-content-center">

            <div class="col-md-4">
                <div class="card h-100 shadow border-light border-2 py-3 bg-light bg-opacity-50">
                    <div class="card-body text-center">
                        <i class="bi bi-gear-wide-connected fs-1 text-danger mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">Alat Lengkap</h5>
                        <p class="text-muted mb-0 small">Standar internasional untuk beban & cardio.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow border-danger border-2 py-3 bg-light bg-opacity-50">
                    <div class="card-body text-center">
                        <i class="bi bi-tags fs-1 text-dark mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">Harga Terjangkau</h5>
                        <p class="text-muted mb-0 small">Mulai dari Rp 150rb/bulan, akses tanpa batas.</p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow border-light border-2 py-3 bg-light bg-opacity-50">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt fs-1 text-danger mb-3 d-block"></i>
                        <h5 class="fw-bold text-dark">Lokasi Strategis</h5>
                        <p class="text-muted mb-0 small">Pusat kota, mudah dijangkau kendaraan apa saja.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
