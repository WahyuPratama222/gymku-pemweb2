<x-layouts.guest :title="'Login'">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

        {{-- Background decorative elements --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/4 -left-32 w-96 h-96 bg-gym-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/4 -right-32 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>

        {{-- Login Card --}}
        <div class="w-full max-w-md relative animate-fade-in-up">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <x-logo size="lg" :with-text="false" class="justify-center mb-4" />
                <h1 class="text-3xl font-bold tracking-tight">
                    Masuk ke <span class="text-gym-primary">Gymku</span>
                </h1>
                <p class="mt-2 text-gym-text-muted text-sm">
                    Selamat datang kembali! Silakan login ke akunmu.
                </p>
            </div>

            {{-- Form Card --}}
            <div class="bg-gym-card/80 backdrop-blur-xl border border-gym-border rounded-2xl p-8 shadow-2xl shadow-black/20">
                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf

                    {{-- Email --}}
                    <x-input
                        name="email"
                        type="email"
                        label="Email"
                        placeholder="nama@email.com"
                        :error="$errors->first('email')"
                        :value="old('email')"
                        required
                        autofocus
                        autocomplete="email"
                        class="animation-delay-100"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Password --}}
                    <x-input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="••••••••"
                        :error="$errors->first('password')"
                        required
                        autocomplete="current-password"
                        class="animation-delay-200"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between animate-fade-in-up animation-delay-300">
                        <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" id="remember" name="remember"
                                class="w-4 h-4 rounded border-gym-border bg-gym-darker text-gym-primary focus:ring-gym-primary/50 focus:ring-offset-0 cursor-pointer">
                            <span class="text-sm text-gym-text-muted group-hover:text-gym-text transition-colors">
                                Ingat saya
                            </span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="animate-fade-in-up animation-delay-400">
                        <x-button type="submit" variant="primary" size="lg" class="w-full">
                            Masuk
                        </x-button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="mt-6 pt-6 border-t border-gym-border text-center">
                    <p class="text-sm text-gym-text-muted">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="font-semibold text-gym-primary hover:text-gym-primary-light transition-colors">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>