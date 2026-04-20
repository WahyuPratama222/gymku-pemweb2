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
                <x-logo size="lg" :with-text="false" class="justify-center mb-4" />
                <h1 class="text-3xl font-bold tracking-tight">
                    Gabung <span class="text-gym-primary">Gymku</span>
                </h1>
                <p class="mt-2 text-gym-text-muted text-sm">
                    Buat akun baru dan mulai perjalanan fitnessmu!
                </p>
            </div>

            {{-- Form Card --}}
            <div class="bg-gym-card/80 backdrop-blur-xl border border-gym-border rounded-2xl p-8 shadow-2xl shadow-black/20">
                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf

                    {{-- Name --}}
                    <x-input
                        name="name"
                        type="text"
                        label="Nama Lengkap"
                        placeholder="John Doe"
                        :error="$errors->first('name')"
                        :value="old('name')"
                        required
                        autofocus
                        autocomplete="name"
                        class="animation-delay-100"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Email --}}
                    <x-input
                        name="email"
                        type="email"
                        label="Email"
                        placeholder="nama@email.com"
                        :error="$errors->first('email')"
                        :value="old('email')"
                        required
                        autocomplete="email"
                        class="animation-delay-100"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Gender --}}
                    <div class="animate-fade-in-up animation-delay-200">
                        <label class="block text-sm font-medium text-gym-text-muted mb-3">
                            Jenis Kelamin
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            <label for="gender-male"
                                   class="relative flex items-center justify-center gap-2 p-3 rounded-xl cursor-pointer border transition-all duration-200
                                   {{ old('gender') === 'Male' ? 'border-gym-primary bg-gym-primary/10 text-gym-primary' : 'border-gym-border bg-gym-darker text-gym-text-muted hover:border-gym-primary/50 hover:text-gym-text' }}">
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
                                   class="relative flex items-center justify-center gap-2 p-3 rounded-xl cursor-pointer border transition-all duration-200
                                   {{ old('gender') === 'Female' ? 'border-gym-primary bg-gym-primary/10 text-gym-primary' : 'border-gym-border bg-gym-darker text-gym-text-muted hover:border-gym-primary/50 hover:text-gym-text' }}">
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
                    <x-input
                        name="password"
                        type="password"
                        label="Password"
                        placeholder="Minimal 8 karakter"
                        :error="$errors->first('password')"
                        required
                        autocomplete="new-password"
                        class="animation-delay-300"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Confirm Password --}}
                    <x-input
                        name="password_confirmation"
                        type="password"
                        label="Konfirmasi Password"
                        placeholder="Ulangi password"
                        required
                        autocomplete="new-password"
                        class="animation-delay-300"
                    >
                        <x-slot name="icon">
                            <svg class="w-5 h-5 text-gym-text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </x-slot>
                    </x-input>

                    {{-- Submit --}}
                    <div class="animate-fade-in-up animation-delay-400">
                        <x-button type="submit" variant="primary" size="lg" class="w-full">
                            Buat Akun
                        </x-button>
                    </div>
                </form>

                {{-- Divider --}}
                <div class="mt-6 pt-6 border-t border-gym-border text-center">
                    <p class="text-sm text-gym-text-muted">
                        Sudah punya akun?
                        <a href="{{ route('login') }}" class="font-semibold text-gym-primary hover:text-gym-primary-light transition-colors">
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