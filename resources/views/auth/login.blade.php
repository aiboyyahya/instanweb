@extends('layouts.auth')

@section('title', 'Masuk')

@section('content')
    <div class="overflow-hidden rounded-panel border border-line bg-surface shadow-sm">
        <div class="h-1.5 w-full bg-gradient-to-r from-brand via-brand-hover to-brand-sky" aria-hidden="true"></div>

        <div class="p-6 sm:p-8">
            <div class="flex items-start gap-3">
                <span
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-card bg-surface-soft text-brand-text">
                    <x-ui.icon name="user" class="h-5 w-5" />
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Akun</p>
                    <h1 class="mt-0.5 flex flex-wrap items-baseline gap-1.5 text-2xl font-bold tracking-tight text-fg">
                        <span>Masuk ke</span>
                        <x-brand-wordmark size="xl" />
                    </h1>
                </div>
            </div>

            <p class="mt-3 text-sm leading-relaxed text-fg-muted">
                Gunakan email dan password akun Anda. Kami akan mengenali anda secara otomatis.
            </p>

            {{-- <div class="mt-4 flex items-start gap-2.5 rounded-card border border-info/25 bg-info-soft px-3.5 py-3 text-xs leading-relaxed text-info">
                <x-ui.icon name="shield" class="mt-0.5 h-4 w-4" />
                <p>Pembeli diarahkan ke beranda, pemilik toko diarahkan ke panel pengelolaan toko.</p>
            </div> --}}

            @if (session('error'))
                <x-ui.alert variant="danger" class="mt-5">{{ session('error') }}</x-ui.alert>
            @endif

            @if ($errors->any())
                <x-ui.alert variant="danger" class="mt-5">
                    Periksa kembali data yang Anda masukkan.
                </x-ui.alert>
            @endif

            <form method="POST" action="{{ route('login.email') }}" class="mt-6 space-y-5">
                @csrf

                <x-ui.field label="Email" name="email" required>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="email" placeholder="nama@email.com"
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3.5 text-sm text-fg placeholder:text-fg-muted">
                </x-ui.field>

                <x-ui.field label="Password" name="password" required>
                    <div class="relative rounded-field border border-line-strong bg-surface overflow-hidden">
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            placeholder="Masukkan password"
                            @error('password') aria-invalid="true" aria-describedby="password-error" @enderror
                            class="h-12 w-full bg-transparent px-3.5 text-sm text-fg placeholder:text-fg-muted pr-11">
                        <button type="button" id="toggle-password-btn"
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

                <label class="flex items-center gap-2.5 text-sm text-fg-secondary">
                    <input type="checkbox" name="remember" value="1"
                        class="h-4.5 w-4.5 rounded border-line-strong text-cta focus:ring-cta">
                    Ingat saya di perangkat ini
                </label>

                <x-ui.button type="submit" size="lg" block>Masuk</x-ui.button>
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
                Masuk dengan Google
            </a>
        </div>
    </div>

    <div class="mt-4 rounded-panel border border-line bg-surface p-4 shadow-sm">
        <div class="mt-3 grid gap-2 sm:grid-cols-2">
            <a href="{{ route('register.form') }}"
                class="iw-lift group flex items-center gap-3 rounded-card border border-line bg-surface-muted p-3 hover:border-cta/40 hover:bg-surface-soft">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-brand-text">
                    <x-ui.icon name="shopping-bag" class="h-4 w-4" />
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-fg">Daftar <br> Pembeli</span>
                    <span class="block text-xs text-fg-muted">Belanja & lacak pesanan</span>
                </span>
                <x-ui.icon name="arrow-right"
                    class="ml-auto h-4 w-4 text-fg-muted transition group-hover:translate-x-0.5 group-hover:text-cta" />
            </a>

            <a href="{{ route('register.store.form') }}"
                class="iw-lift group flex items-center gap-3 rounded-card border border-line bg-surface-muted p-3 hover:border-cta/40 hover:bg-surface-soft">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-surface text-info">
                    <x-ui.icon name="shield" class="h-4 w-4" />
                </span>
                <span class="min-w-0">
                    <span class="block text-sm font-semibold text-fg">Daftar <br>Toko</span>
                    <span class="block text-xs text-fg-muted">Jual produk UMKM Anda</span>
                </span>
                <x-ui.icon name="arrow-right"
                    class="ml-auto h-4 w-4 text-fg-muted transition group-hover:translate-x-0.5 group-hover:text-cta" />
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const password = document.getElementById('password');
            const button = document.getElementById('toggle-password-btn');

            if (!password || !button) return;

            const iconShow = button.querySelector('[data-icon-show]');
            const iconHide = button.querySelector('[data-icon-hide]');

            button.addEventListener('click', () => {
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
        });
    </script>
@endpush
