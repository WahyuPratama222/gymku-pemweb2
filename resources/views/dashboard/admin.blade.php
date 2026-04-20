<x-layouts.app :title="'Admin Dashboard'">
    <div class="space-y-8">
        {{-- Page Header --}}
        <div class="animate-fade-in-up">
            <h1 class="text-3xl font-bold tracking-tight">
                Dashboard <span class="text-gym-primary">Admin</span>
            </h1>
            <p class="mt-2 text-gym-text-muted">
                Selamat datang, {{ Auth::user()->name }}! Kelola gym kamu dari sini.
            </p>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <x-stat-card 
                title="Total Member"
                value="—"
                iconBg="gym-primary"
                delay="100"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Paket Aktif"
                value="—"
                iconBg="amber"
                delay="200"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Pendapatan"
                value="—"
                iconBg="success"
                delay="300"
            >
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </x-slot>
            </x-stat-card>
        </div>

        {{-- Quick Actions --}}
        <div class="bg-gym-card border border-gym-border rounded-2xl p-6 animate-fade-in-up animation-delay-400">
            <h2 class="text-lg font-semibold mb-4">Aksi Cepat</h2>
            <p class="text-gym-text-muted text-sm">
                Fitur dashboard admin akan segera dikembangkan. Saat ini kamu berhasil login sebagai admin! 🎉
            </p>
        </div>
    </div>
</x-layouts.app>