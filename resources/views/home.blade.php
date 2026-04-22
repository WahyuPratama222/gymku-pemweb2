@extends('layouts.app')

@section('title', 'Beranda — Gymku')

@section('content')
<div class="bg-dark py-5">
    <div class="container">
        <!-- Bagian kiri -->
        <div class="row align-items-center g-4 mb-5">
            <div class="col-md-7 text-white">
                <h1 class="display-4 fw-bold text-white">Selamat Datang di <span class="text-warning">Gymku</span></h1>
                <p class="lead opacity-75 mt-3">
                    Gymku adalah gym yang menyediakan alat standar internasional untuk mendukung latihan beban dan
                    cardio Anda.
                </p>
            </div>

            <!-- Bagian kanan -->
            <div class="col-md-4 d-flex justify-content-end">
                <div class="position-relative d-inline-block p-4 text-center">
                    <i class="bi bi-trophy-fill text-warning display-1 mb-3 d-block"></i>
                    <div class="mt-2">
                        <h4 class="text-white fw-bold mb-1">Palembang <span class="text-warning">#1</span></h4>
                        <p class="text-white-50 small">Premium Gym Experience</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Cards -->
        <div class="row g-4 justify-content-center">

            <!-- Card 1 -->
            <div class="col-md-4">
                <div class="card h-100 shadow border-white border-2 py-3 bg-secondary bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-gear-wide-connected fs-1 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold text-white">Alat Lengkap</h5>
                        <p class="text-white-50 mb-0 small">Standar internasional untuk beban & cardio.</p>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="col-md-4">
                <div class="card h-100 shadow border-warning border-2 py-3 bg-secondary bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-tags fs-1 text-white mb-3 d-block"></i>
                        <h5 class="fw-bold text-white">Harga Terjangkau</h5>
                        <p class="text-white-50 mb-0 small">Mulai dari Rp 150rb/bulan, akses tanpa batas.</p>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="col-md-4">
                <div class="card h-100 shadow border-white border-2 py-3 bg-secondary bg-opacity-10">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt fs-1 text-warning mb-3 d-block"></i>
                        <h5 class="fw-bold text-white">Lokasi Strategis</h5>
                        <p class="text-white-50 mb-0 small">Pusat kota, mudah dijangkau kendaraan apa saja.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection