@extends('layouts.auth')

@section('title', 'Daftar Akun Pembeli')

@section('content')
    <div class="overflow-hidden rounded-panel border border-line bg-surface shadow-sm">
        <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-hover to-brand-sky" aria-hidden="true"></div>

        <div class="p-6 sm:p-8">
            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Akun baru</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-fg">Buat akun pembeli</h1>
            <p class="mt-2 text-sm leading-relaxed text-fg-muted">
                @if (config('verification.enabled', false))
                    Cukup isi nama, email, dan password. Kami akan mengirim kode verifikasi untuk mengamankan akun Anda.
                @else
                    Cukup isi nama, email, dan password untuk mulai berbelanja.
                @endif
            </p>

            {{-- Pilihan jenis akun --}}
            <div class="mt-5 grid gap-2 sm:grid-cols-2" role="group" aria-label="Pilih jenis akun">
                <div class="flex items-center gap-3 rounded-card border-2 border-cta/50 bg-surface-soft p-3"
                    aria-current="true">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-brand-text">
                        <x-ui.icon name="shopping-bag" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-fg">Pembeli</span>
                        <span class="block text-xs text-fg-muted">Untuk berbelanja</span>
                    </span>
                    <x-ui.icon name="check-circle" class="ml-auto h-4 w-4 text-cta" />
                </div>

                <a href="{{ route('register.store.form') }}"
                    class="iw-lift group flex items-center gap-3 rounded-card border border-line bg-surface-muted p-3 hover:border-cta/40 hover:bg-surface-soft">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-info">
                        <x-ui.icon name="shield" class="h-4 w-4" />
                    </span>
                    <span class="min-w-0">
                        <span class="block text-sm font-semibold text-fg">Pemilik toko</span>
                        <span class="block text-xs text-fg-muted">Buka toko online</span>
                    </span>
                    <x-ui.icon name="arrow-right"
                        class="ml-auto h-4 w-4 text-fg-muted transition group-hover:translate-x-0.5 group-hover:text-cta" />
                </a>
            </div>

            @if (session('error'))
                <x-ui.alert variant="danger" class="mt-5">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert variant="danger" class="mt-5">
                    Periksa kembali data yang Anda masukkan.
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('register.email') }}" class="mt-6 space-y-5" data-register-form>
                @csrf

                <x-ui.field label="Nama lengkap" name="name" required>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                        autocomplete="name" placeholder="Contoh: Siti Rahma"
                        @error('name') aria-invalid="true" aria-describedby="name-error" @enderror
                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                </x-ui.field>

                <x-ui.field label="Email" name="email" required>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        autocomplete="email" placeholder="nama@email.com"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                </x-ui.field>

                <x-ui.field label="Nomor telepon" name="phone_number"
                    hint="{{ config('verification.enabled', false) ? 'Opsional. Kode verifikasi SMS dikirim ke nomor ini.' : 'Opsional.' }}">
                    <input type="tel" id="phone_number" name="phone_number" value="{{ old('phone_number') }}"
                        autocomplete="tel" inputmode="tel" placeholder="08123456789"
                        @error('phone_number') aria-invalid="true" aria-describedby="phone_number-error" @enderror
                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                </x-ui.field>

                <x-ui.field label="Password" name="password" required hint="Minimal 8 karakter.">
                    <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            placeholder="Buat password" data-password-input
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                        <button type="button" id="toggle-register-password-btn"
                            class="absolute inset-y-0 right-0 z-10 flex cursor-pointer items-center justify-center px-3.5 text-fg-muted transition hover:text-brand-text focus:outline-none"
                            aria-label="Tampilkan password" aria-pressed="false">
                            <span data-icon-show>
                                <x-ui.icon name="eye" class="h-5 w-5" />
                            </span>
                            <span data-icon-hide class="hidden">
                                <x-ui.icon name="eye-off" class="h-5 w-5" />
                            </span>
                        </button>
                    </div>

                    <div class="mt-2 flex items-center gap-2" aria-live="polite">
                        <div class="flex flex-1 gap-1" aria-hidden="true">
                            <span class="h-1.5 flex-1 rounded-full bg-line" data-strength-bar></span>
                            <span class="h-1.5 flex-1 rounded-full bg-line" data-strength-bar></span>
                            <span class="h-1.5 flex-1 rounded-full bg-line" data-strength-bar></span>
                        </div>
                        <span class="text-[11px] font-medium text-fg-muted" data-strength-label>Kekuatan password</span>
                    </div>
                </x-ui.field>

                <x-ui.field label="Konfirmasi password" name="password_confirmation" required>
                    <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password" placeholder="Ulangi password"
                            @error('password_confirmation') aria-invalid="true" aria-describedby="password_confirmation-error" @enderror
                            class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                        <button type="button" id="toggle-confirm-password-btn"
                            class="absolute inset-y-0 right-0 z-10 flex cursor-pointer items-center justify-center px-3.5 text-fg-muted transition hover:text-brand-text focus:outline-none"
                            aria-label="Tampilkan password" aria-pressed="false">
                            <span data-icon-show>
                                <x-ui.icon name="eye" class="h-5 w-5" />
                            </span>
                            <span data-icon-hide class="hidden">
                                <x-ui.icon name="eye-off" class="h-5 w-5" />
                            </span>
                        </button>
                    </div>
                </x-ui.field>

                <x-ui.button type="submit" size="lg" block>Daftar sebagai pembeli</x-ui.button>
            </form>

            <div class="my-6 flex items-center gap-3">
                <div class="h-px flex-1 bg-line"></div>
                <span class="text-xs font-medium text-fg-muted">atau</span>
                <div class="h-px flex-1 bg-line"></div>
            </div>

            <a href="{{ route('google.redirect') }}"
                class="inline-flex min-h-12 w-full items-center justify-center gap-3 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path fill="#4285F4"
                        d="M21.35 11.1H12v2.8h5.35c-.23 1.4-1.14 2.6-2.43 3.4v2.8h3.93c2.3-2.12 3.65-5.23 3.65-8.99 0-.6-.06-1.18-.18-1.75z" />
                    <path fill="#34A853"
                        d="M12 22c2.7 0 4.96-.9 6.62-2.45l-3.93-2.8c-.88.6-2.03.95-2.69.95-2.06 0-3.81-1.38-4.44-3.24H3.06v2.03C4.7 19.96 8.07 22 12 22z" />
                    <path fill="#FBBC05" d="M7.56 13.46a6.24 6.24 0 010-3.92V7.51H3.06a9.98 9.98 0 000 8.98l4.5-2.03z" />
                    <path fill="#EA4335"
                        d="M12 6.6c1.47 0 2.78.5 3.82 1.48l2.86-2.86C16.96 3.7 14.7 3 12 3 8.07 3 4.7 5.04 3.06 7.97l4.5 2.03C8.19 7.98 9.94 6.6 12 6.6z" />
                </svg>
                Daftar dengan Google
            </a>

            <p class="mt-6 text-center text-sm text-fg-muted">
                Sudah punya akun?
                <a href="{{ route('login.form') }}"
                    class="font-semibold text-info underline underline-offset-2 hover:text-fg">Masuk</a>
            </p>
        </div>
    </div>

    <p class="mt-4 text-center text-xs leading-relaxed text-fg-muted">
        Dengan mendaftar, Anda menyetujui penggunaan data untuk keperluan akun dan pesanan di toko ini.
    </p>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.querySelector('[data-password-input]');

            if (!input) {
                return;
            }

            const bars = Array.from(document.querySelectorAll('[data-strength-bar]'));
            const label = document.querySelector('[data-strength-label]');
            const colors = ['bg-danger', 'bg-warning', 'bg-success'];
            const labels = ['Lemah', 'Sedang', 'Kuat'];

            input.addEventListener('input', function() {
                const value = input.value;
                let score = 0;

                if (value.length >= 8) score += 1;
                if (/[A-Z]/.test(value) && /[a-z]/.test(value)) score += 1;
                if (/\d/.test(value) && /[^A-Za-z0-9]/.test(value)) score += 1;

                bars.forEach(function(bar, index) {
                    bar.classList.remove('bg-line', 'bg-danger', 'bg-warning', 'bg-success');

                    if (value.length === 0) {
                        bar.classList.add('bg-line');
                    } else if (index < Math.max(score, 1)) {
                        bar.classList.add(colors[Math.max(score, 1) - 1]);
                    } else {
                        bar.classList.add('bg-line');
                    }
                });

                if (label) {
                    label.textContent = value.length === 0 ? 'Kekuatan password' : labels[Math.max(score,
                        1) - 1];
                }
            });

            function setupToggle(buttonId, inputId) {
                const password = document.getElementById(inputId);
                const button = document.getElementById(buttonId);

                if (!password || !button) return;

                const iconShow = button.querySelector('[data-icon-show]');
                const iconHide = button.querySelector('[data-icon-hide]');

                button.addEventListener('click', function() {
                    const visible = password.type === 'password';

                    password.type = visible ? 'text' : 'password';

                    iconShow.classList.toggle('hidden', visible);
                    iconHide.classList.toggle('hidden', !visible);

                    button.setAttribute('aria-pressed', String(visible));
                    button.setAttribute(
                        'aria-label',
                        visible ? 'Sembunyikan password' : 'Tampilkan password'
                    );
                });
            }

            setupToggle('toggle-register-password-btn', 'password');
            setupToggle('toggle-confirm-password-btn', 'password_confirmation');
        });
    </script>
@endpush
