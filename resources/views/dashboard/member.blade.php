<x-layouts.app :title="'Member Dashboard'">
    <div class="space-y-8">
        {{-- Welcome Header --}}
        <div class="animate-fade-in-up">
            <h1 class="text-3xl font-bold tracking-tight">
                Halo, <span class="text-gym-primary">{{ Auth::user()->name }}</span>! 👋
            </h1>
            <p class="mt-2 text-gym-text-muted">
                Selamat datang di Gymku. Pantau progres dan kelola membership kamu.
            </p>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stat-card 
                title="Status"
                value="Belum aktif"
                iconBg="gym-primary"
                delay="100"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Catatan Progres"
                value="0"
                iconBg="amber"
                delay="200"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Sisa Hari"
                value="—"
                iconBg="success"
                delay="300"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </x-slot>
            </x-stat-card>
        </div>

        {{-- Info Card --}}
        <div class="bg-gym-card border border-gym-border rounded-2xl p-6 animate-fade-in-up animation-delay-400">
            <h2 class="text-lg font-semibold mb-4">Mulai Perjalanan Fitness Kamu 🏋️</h2>
            <p class="text-gym-text-muted text-sm">
                Dashboard member akan segera dikembangkan lebih lengkap. Saat ini kamu berhasil terdaftar sebagai member Gymku!
            </p>
        </div>
    </div>
</x-layouts.app>