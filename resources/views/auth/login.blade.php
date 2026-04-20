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
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-gym-primary to-amber-500 shadow-2xl shadow-gym-primary/25 mb-4 animate-pulse-glow">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Masuk ke <span class="text-gym-primary">Gymku</span>
                </h1>
                <p class="mt-2 text-gym-text-muted text-sm">
                    Selamat datang kembali! Silakan login ke akunmu.
                </p>
            </div>

            {{-- Form Card --}}
            <div class="bg-gym-card/80 backdrop-blur-xl border border-gym-border rounded-2xl p-8 shadow-2xl shadow-black/20">
                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="login-form">
                    @csrf

                    {{-- Email --}}
                    <div class="animate-fade-in-up animation-delay-100">
                        <label for="email" class="block text-sm font-medium text-gym-text-muted mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autofocus
                                   autocomplete="email"
                                   placeholder="nama@email.com"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl
                                          bg-gym-darker border border-gym-border
                                          text-gym-text placeholder-gym-text-muted/50
                                          focus:outline-none focus:border-gym-primary focus:ring-1 focus:ring-gym-primary/50
                                          transition-all duration-200
                                          @error('email') border-gym-error ring-1 ring-gym-error/50 @enderror">
                        </div>
                        @error('email')
                            <p class="mt-2 text-sm text-gym-error flex items-center gap-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="animate-fade-in-up animation-delay-200">
                        <label for="password" class="block text-sm font-medium text-gym-text-muted mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl
                                          bg-gym-darker border border-gym-border
                                          text-gym-text placeholder-gym-text-muted/50
                                          focus:outline-none focus:border-gym-primary focus:ring-1 focus:ring-gym-primary/50
                                          transition-all duration-200
                                          @error('password') border-gym-error ring-1 ring-gym-error/50 @enderror">
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-gym-error flex items-center gap-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Remember Me --}}
                    <div class="flex items-center justify-between animate-fade-in-up animation-delay-300">
                        <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox"
                                   id="remember"
                                   name="remember"
                                   class="w-4 h-4 rounded border-gym-border bg-gym-darker
                                          text-gym-primary focus:ring-gym-primary/50 focus:ring-offset-0
                                          cursor-pointer">
                            <span class="text-sm text-gym-text-muted group-hover:text-gym-text transition-colors">
                                Ingat saya
                            </span>
                        </label>
                    </div>

                    {{-- Submit --}}
                    <div class="animate-fade-in-up animation-delay-400">
                        <button type="submit"
                                id="btn-login"
                                class="w-full py-3 px-4 rounded-xl font-semibold text-white
                                       bg-gradient-to-r from-gym-primary to-amber-500
                                       hover:from-gym-primary-dark hover:to-amber-600
                                       focus:outline-none focus:ring-2 focus:ring-gym-primary/50 focus:ring-offset-2 focus:ring-offset-gym-card
                                       shadow-lg shadow-gym-primary/25 hover:shadow-gym-primary/40
                                       transform hover:scale-[1.02] active:scale-[0.98]
                                       transition-all duration-200 cursor-pointer">
                            Masuk
                        </button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="mt-6 pt-6 border-t border-gym-border text-center">
                    <p class="text-sm text-gym-text-muted">
                        Belum punya akun?
                        <a href="{{ route('register') }}"
                           class="font-semibold text-gym-primary hover:text-gym-primary-light transition-colors">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-layouts.guest>
