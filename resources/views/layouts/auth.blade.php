@php
    $authStore = store();
    $authStoreName = $authStore->store_name ?? 'instanweb';
    $asideTitle = trim($__env->yieldContent('aside_title'));
    $asideDescription = trim($__env->yieldContent('aside_description'));
    $formWidth = trim($__env->yieldContent('form_width')) ?: 'max-w-md';
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e56fc">
    <link rel="icon" type="image/png" href="{{ asset('images/instanweb-logo.png') }}">
    <title>@yield('title', 'Masuk') | {{ $authStoreName }}</title>

    <script>
        (function() {
            try {
                var saved = localStorage.getItem('iw-theme');
                var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = saved === 'light' || saved === 'dark' ? saved : (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
                document.documentElement.style.colorScheme = theme;
            } catch (error) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-surface-muted text-fg">
    <a href="#auth-content" class="skip-link">Lewati ke formulir</a>

    <div class="grid min-h-screen lg:grid-cols-2">
        {{-- Panel identitas toko --}}
        <aside
            class="relative hidden overflow-hidden bg-inverse p-10 text-fg-inverse lg:flex lg:flex-col lg:justify-between">
            <div aria-hidden="true"
                class="pointer-events-none absolute -right-24 -top-24 h-72 w-72 rounded-full bg-brand-sky/20 blur-3xl">
            </div>
            <div aria-hidden="true"
                class="pointer-events-none absolute -bottom-32 -left-20 h-80 w-80 rounded-full bg-brand/20 blur-3xl">
            </div>

            <a href="{{ route('home') }}" class="iw-brand-link relative flex items-center gap-3 rounded-md">
                <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560" height="564"
                    class="iw-brand-mark h-12 w-12" decoding="async">
                <x-brand-wordmark variant="inverse" size="xl" />

                {{-- @if ($authStore)
                    @if ($authStore->logo)
                        <img src="{{ asset('storage/' . $authStore->logo) }}" alt=""
                            class="iw-brand-mark h-12 w-12 rounded-xl border border-white/20 object-cover">
                    @else
                        <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560" height="564"
                            class="iw-brand-mark h-12 w-12" decoding="async">
                    @endif
                    <span class="flex flex-col leading-tight">
                        <span class="text-lg font-bold">{{ $authStoreName }}</span>
                        <span class="mt-1 flex items-center gap-1.5 text-xs text-white/60">
                            <span>via</span>
                            <x-brand-wordmark variant="inverse" size="xs" />
                        </span>
                    </span>
                @else
                    <x-brand variant="inverse" size="lg" />
                @endif --}}
            </a>

            <div class="relative max-w-md">
                <h2 class="text-3xl font-extrabold leading-tight tracking-tight text-white">
                    {!! $asideTitle !== '' ? $asideTitle : 'Belanja produk lokal,<br>dukung usaha kecil.</br>' !!}
                </h2>
                <p class="mt-4 text-sm leading-relaxed text-white/70">
                    {{ $asideDescription !== '' ? $asideDescription : 'Masuk untuk menyimpan riwayat pesanan, melanjutkan checkout, dan mengirim ulasan produk yang Anda terima.' }}
                </p>

                @hasSection('aside_points')
                    <div class="mt-8 space-y-3 text-sm text-white/80">
                        @yield('aside_points')
                    </div>
                @else
                    <ul class="mt-8 space-y-3 text-sm text-white/80">
                        <li class="flex items-start gap-3">
                            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
                            <span>Keranjang dan checkout produk mu.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
                            <span>Pantau status pesanan kapan saja.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <x-ui.icon name="check-circle" class="mt-0.5 h-5 w-5 text-brand-sky" />
                            <span>Hubungi penjual langsung melalui WhatsApp.</span>
                        </li>
                    </ul>
                @endif
            </div>

            <p class="relative text-xs text-white/50">
                &copy; {{ date('Y') }} GTNIC Web Development.
            </p>
        </aside>

        {{-- Formulir --}}
        <main id="auth-content" tabindex="-1" class="flex flex-col">
            <div class="flex items-center justify-between gap-3 px-4 py-4 sm:px-8">
                <a href="{{ route('home') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-field px-2 text-sm font-semibold text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" />
                    Kembali ke Beranda
                </a>

                <button type="button" data-theme-toggle aria-pressed="false" title="Ganti mode tampilan"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line bg-surface text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                    <x-ui.icon name="moon" data-theme-icon="moon" class="h-5 w-5 dark:hidden" />
                    <x-ui.icon name="sun" data-theme-icon="sun" class="hidden h-5 w-5 dark:block" />
                    <span class="sr-only" data-theme-label>Mode gelap</span>
                </button>
            </div>

            <div class="flex flex-1 items-center justify-center px-4 py-8 sm:px-8">
                <div class="iw-fade-in w-full {{ $formWidth }}">
                    <div class="mb-6 flex items-center gap-3 lg:hidden">
                        <a href="{{ route('home') }}" class="iw-brand-link flex items-center gap-2.5 rounded-md">
                            @if ($authStore)
                                @if ($authStore->logo)
                                    <img src="{{ asset('storage/' . $authStore->logo) }}" alt=""
                                        class="iw-brand-mark h-10 w-10 rounded-xl border border-line object-cover">
                                @else
                                    <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560"
                                        height="564" class="iw-brand-mark h-10 w-10" decoding="async">
                                @endif
                                <span class="flex flex-col leading-tight">
                                    <span class="text-base font-bold text-fg">{{ $authStoreName }}</span>
                                    <span class="mt-0.5 flex items-center gap-1 text-[10px] text-fg-muted">
                                        <span>via</span>
                                        <x-brand-wordmark size="xs" />
                                    </span>
                                </span>
                            @else
                                <x-brand size="md" />
                            @endif
                        </a>
                    </div>

                    @yield('content')
                </div>
            </div>
        </main>
    </div>
    @stack('scripts')
</body>

</html>
