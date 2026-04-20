<x-layouts.guest :title="'Daftar'">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

        {{-- Background decorative elements --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-1/3 -right-32 w-96 h-96 bg-gym-primary/5 rounded-full blur-3xl"></div>
            <div class="absolute bottom-1/3 -left-32 w-96 h-96 bg-amber-500/5 rounded-full blur-3xl"></div>
        </div>

        {{-- Register Card --}}
        <div class="w-full max-w-md relative animate-fade-in-up">

            {{-- Logo --}}
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-gym-primary to-amber-500 shadow-2xl shadow-gym-primary/25 mb-4 animate-pulse-glow">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold tracking-tight">
                    Gabung <span class="text-gym-primary">Gymku</span>
                </h1>
                <p class="mt-2 text-gym-text-muted text-sm">
                    Buat akun baru dan mulai perjalanan fitnessmu!
                </p>
            </div>

            {{-- Form Card --}}
            <div class="bg-gym-card/80 backdrop-blur-xl border border-gym-border rounded-2xl p-8 shadow-2xl shadow-black/20">
                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="register-form">
                    @csrf

                    {{-- Name --}}
                    <div class="animate-fade-in-up animation-delay-100">
                        <label for="name" class="block text-sm font-medium text-gym-text-muted mb-2">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   required
                                   autofocus
                                   autocomplete="name"
                                   placeholder="John Doe"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl
                                          bg-gym-darker border border-gym-border
                                          text-gym-text placeholder-gym-text-muted/50
                                          focus:outline-none focus:border-gym-primary focus:ring-1 focus:ring-gym-primary/50
                                          transition-all duration-200
                                          @error('name') border-gym-error ring-1 ring-gym-error/50 @enderror">
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-gym-error flex items-center gap-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

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

                    {{-- Gender --}}
                    <div class="animate-fade-in-up animation-delay-200">
                        <label class="block text-sm font-medium text-gym-text-muted mb-3">
                            Jenis Kelamin
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label for="gender-male"
                                   class="relative flex items-center justify-center gap-2 p-3 rounded-xl cursor-pointer
                                          border transition-all duration-200
                                          {{ old('gender') === 'Male'
                                              ? 'border-gym-primary bg-gym-primary/10 text-gym-primary'
                                              : 'border-gym-border bg-gym-darker text-gym-text-muted hover:border-gym-primary/50 hover:text-gym-text' }}">
                                <input type="radio" id="gender-male" name="gender" value="Male"
                                       {{ old('gender') === 'Male' ? 'checked' : '' }}
                                       class="sr-only peer"
                                       onchange="updateGenderUI()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium">Laki-laki</span>
                            </label>

                            <label for="gender-female"
                                   class="relative flex items-center justify-center gap-2 p-3 rounded-xl cursor-pointer
                                          border transition-all duration-200
                                          {{ old('gender') === 'Female'
                                              ? 'border-gym-primary bg-gym-primary/10 text-gym-primary'
                                              : 'border-gym-border bg-gym-darker text-gym-text-muted hover:border-gym-primary/50 hover:text-gym-text' }}">
                                <input type="radio" id="gender-female" name="gender" value="Female"
                                       {{ old('gender') === 'Female' ? 'checked' : '' }}
                                       class="sr-only peer"
                                       onchange="updateGenderUI()">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                <span class="text-sm font-medium">Perempuan</span>
                            </label>
                        </div>
                        @error('gender')
                            <p class="mt-2 text-sm text-gym-error flex items-center gap-1">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/>
                                </svg>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="animate-fade-in-up animation-delay-300">
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
                                   autocomplete="new-password"
                                   placeholder="Minimal 8 karakter"
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

                    {{-- Confirm Password --}}
                    <div class="animate-fade-in-up animation-delay-300">
                        <label for="password_confirmation" class="block text-sm font-medium text-gym-text-muted mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="Ulangi password"
                                   class="w-full pl-12 pr-4 py-3 rounded-xl
                                          bg-gym-darker border border-gym-border
                                          text-gym-text placeholder-gym-text-muted/50
                                          focus:outline-none focus:border-gym-primary focus:ring-1 focus:ring-gym-primary/50
                                          transition-all duration-200">
                        </div>
                    </div>

                    {{-- Submit --}}
                    <div class="animate-fade-in-up animation-delay-400">
                        <button type="submit"
                                id="btn-register"
                                class="w-full py-3 px-4 rounded-xl font-semibold text-white
                                       bg-gradient-to-r from-gym-primary to-amber-500
                                       hover:from-gym-primary-dark hover:to-amber-600
                                       focus:outline-none focus:ring-2 focus:ring-gym-primary/50 focus:ring-offset-2 focus:ring-offset-gym-card
                                       shadow-lg shadow-gym-primary/25 hover:shadow-gym-primary/40
                                       transform hover:scale-[1.02] active:scale-[0.98]
                                       transition-all duration-200 cursor-pointer">
                            Buat Akun
                        </button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="mt-6 pt-6 border-t border-gym-border text-center">
                    <p class="text-sm text-gym-text-muted">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                           class="font-semibold text-gym-primary hover:text-gym-primary-light transition-colors">
                            Masuk di sini
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Gender radio button UI update script --}}
    <script>
        function updateGenderUI() {
            const labels = document.querySelectorAll('label[for^="gender-"]');
            labels.forEach(label => {
                const radio = label.querySelector('input[type="radio"]');
                if (radio.checked) {
                    label.className = label.className
                        .replace('border-gym-border', 'border-gym-primary')
                        .replace('bg-gym-darker', 'bg-gym-primary/10')
                        .replace('text-gym-text-muted', 'text-gym-primary');
                } else {
                    label.className = label.className
                        .replace('border-gym-primary', 'border-gym-border')
                        .replace('bg-gym-primary/10', 'bg-gym-darker')
                        .replace('text-gym-primary', 'text-gym-text-muted');
                }
            });
        }
    </script>
</x-layouts.guest>
