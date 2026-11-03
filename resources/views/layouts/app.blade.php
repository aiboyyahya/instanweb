@php
    $store = store();
    $cartItems = session()->get('cart', []);
    $cartCount = count($cartItems);
    $storeName = $store->store_name ?? 'instanweb';
    $whatsappNumber = $store && !empty($store->whatsapp) ? preg_replace('/[^0-9]/', '', $store->whatsapp) : null;
@endphp
<!DOCTYPE html>
<html lang="id" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1e56fc">
    <link rel="icon" type="image/png" href="{{ asset('images/instanweb-logo.png') }}">
    <title>@yield('title', $storeName . ' | Pasar Produk UMKM Indonesia')</title>

    {{-- Terapkan tema sebelum paint agar tidak ada kilatan warna (brief §7.5). --}}
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
    @stack('head')
</head>

<body class="min-h-screen bg-surface-muted text-fg">
    <a href="#main-content" class="skip-link">Lewati ke konten utama</a>

    <header
        class="sticky top-0 z-50 border-b border-line bg-surface/95 backdrop-blur supports-[backdrop-filter]:bg-surface/85">
        <div class="mx-auto flex h-16 max-w-7xl items-center gap-3 px-4 sm:px-6 lg:px-8">
            {{-- Brand --}}
            <a href="{{ route('home') }}" class="iw-brand-link flex shrink-0 items-center gap-2.5 rounded-md"
                aria-label="{{ $store ? $storeName . ' — Beranda' : 'instanweb — Beranda' }}">
                <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560" height="564"
                    class="iw-brand-mark h-10 w-10" decoding="async">
                <x-brand-wordmark size="xl" />
                {{-- {{-- @if ($store)
                    @if ($store->logo)
                        <img src="{{ asset('storage/' . $store->logo) }}" alt=""
                            class="iw-brand-mark h-10 w-10 rounded-xl border border-line object-cover">
                            <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560" height="564"
                                class="iw-brand-mark h-10 w-10" decoding="async">
                    @else
                    @endif

                <span class="flex min-w-0 flex-col leading-none">
                    <span
                        class="truncate text-base font-bold tracking-tight text-fg sm:text-lg">{{ $storeName }}</span>
                    <span class="mt-1.5 hidden items-center gap-1 text-[11px] text-fg-muted sm:flex">
                        <span>via</span>
                        <x-brand-wordmark size="xs" />
                    </span>
                </span>
            @else
                <x-brand size="md" />
                @endif --}}
            </a>
            @if (!request()->routeIs('selamatdatang'))

                {{-- Navigasi desktop --}}
                <nav class="ml-2 hidden items-center gap-1 lg:flex" aria-label="Navigasi utama">
                    @php
                        $navLinks = [
                            ['label' => 'Beranda', 'url' => route('home'), 'active' => request()->is('home')],
                            ['label' => 'Produk', 'url' => route('products'), 'active' => request()->is('produk*')],
                            [
                                'label' => 'Artikel',
                                'url' => route('articles.index'),
                                'active' => request()->is('artikel*'),
                            ],
                            ['label' => 'Kontak', 'url' => route('kontak'), 'active' => request()->is('kontak*')],
                        ];
                    @endphp

                    @foreach ($navLinks as $link)
                        <a href="{{ $link['url'] }}" @if ($link['active']) aria-current="page" @endif
                            class="relative rounded-md px-3 py-2 text-sm font-medium transition-colors {{ $link['active'] ? 'text-fg' : 'text-fg-secondary hover:bg-surface-soft hover:text-fg' }}">
                            {{ $link['label'] }}
                            @if ($link['active'])
                                <span class="absolute inset-x-3 -bottom-[21px] h-0.5 rounded-full bg-cta"
                                    aria-hidden="true"></span>
                            @endif
                        </a>
                    @endforeach
                </nav>
            @endif

            <div class="ml-auto flex items-center gap-1.5 sm:gap-2">
                @if (!request()->routeIs('selamatdatang'))
                    {{-- Pencarian (tablet & desktop) --}}
                    <form action="{{ route('products') }}" method="GET" role="search"
                        class="hidden items-center md:flex">
                        <label for="header-search" class="sr-only">Cari produk</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-fg-muted">
                                <x-ui.icon name="search" class="h-4 w-4" />
                            </span>
                            <input type="search" id="header-search" name="search" value="{{ request('search') }}"
                                placeholder="Cari produk..."
                                class="h-10 w-40 rounded-full border border-line-strong bg-surface pl-9 pr-3 text-sm text-fg placeholder:text-fg-muted lg:w-48 xl:w-56">
                        </div>
                    </form>
                @endif

                {{-- Toggle tema --}}
                <button type="button" data-theme-toggle aria-pressed="false" title="Ganti mode tampilan"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line bg-surface text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                    <x-ui.icon name="moon" data-theme-icon="moon" class="h-5 w-5 dark:hidden" />
                    <x-ui.icon name="sun" data-theme-icon="sun" class="hidden h-5 w-5 dark:block" />
                    <span class="sr-only" data-theme-label>Mode gelap</span>
                </button>

                {{-- Keranjang --}}
                @if (!request()->routeIs('selamatdatang'))
                    <a href="{{ route('cart') }}"
                        class="relative inline-flex h-11 w-11 items-center justify-center rounded-full border border-line bg-surface text-fg-secondary transition hover:bg-surface-soft hover:text-fg"
                        aria-label="Keranjang belanja, {{ $cartCount }} produk">
                        <x-ui.icon name="shopping-bag" class="h-5 w-5" />
                        @if ($cartCount > 0)
                            <span
                                class="absolute -right-0.5 -top-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-cta px-1 text-[11px] font-bold text-on-cta">{{ $cartCount }}</span>
                        @endif
                    </a>
                @endif

                {{-- Akun --}}
                @auth
                    <div class="relative hidden lg:block">
                        <button type="button" data-profile-menu-button aria-expanded="false" aria-controls="profile-menu"
                            class="inline-flex h-11 items-center gap-2 rounded-full border border-line bg-surface pl-1.5 pr-2.5 text-sm font-medium text-fg transition hover:bg-surface-soft">
                            @if (Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" alt=""
                                    class="h-8 w-8 rounded-full object-cover">
                            @else
                                <span
                                    class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-soft text-xs font-bold text-fg-secondary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            @endif
                            <span class="hidden max-w-24 truncate xl:inline">{{ Auth::user()->name }}</span>
                            <x-ui.icon name="chevron-down" class="h-4 w-4 text-fg-muted" />
                        </button>

                        <div id="profile-menu" data-profile-menu
                            class="absolute right-0 top-full z-50 mt-2 hidden w-52 overflow-hidden rounded-card border border-line bg-surface p-1.5 shadow-lg">
                            <div class="border-b border-line px-3 pb-2 pt-1.5">
                                <p class="truncate text-sm font-semibold text-fg">{{ Auth::user()->name }}</p>
                                <p class="truncate text-xs text-fg-muted">{{ Auth::user()->email }}</p>
                            </div>
                            <a href="{{ route('profil') }}"
                                class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                                <x-ui.icon name="user" class="h-4 w-4" /> Profil Saya
                            </a>
                            <a href="{{ route('orders') }}"
                                class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                                <x-ui.icon name="package" class="h-4 w-4" /> Pesanan Saya
                            </a>
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ url('/admin') }}"
                                    class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                                    <x-ui.icon name="shield" class="h-4 w-4" /> Panel Admin
                                </a>
                            @else
                                <a href="{{ route('register.store.form') }}"
                                    class="flex items-center gap-2 rounded-md px-3 py-2.5 text-sm text-fg-secondary transition hover:bg-surface-soft hover:text-fg">
                                    <x-ui.icon name="shield" class="h-4 w-4" /> Buat Toko
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-line pt-1">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-2 rounded-md px-3 py-2.5 text-left text-sm text-danger transition hover:bg-danger-soft">
                                    <x-ui.icon name="arrow-right" class="h-4 w-4" /> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login.form') }}"
                        class="hidden h-11 items-center justify-center rounded-full border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft sm:inline-flex">
                        Masuk
                    </a>
                @endauth

                {{-- Menu mobile --}}
                <button type="button" data-mobile-menu-button aria-expanded="false" aria-controls="mobile-nav"
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line bg-surface text-fg transition hover:bg-surface-soft lg:hidden">
                    <x-ui.icon name="menu" class="h-5 w-5" />
                    <span class="sr-only">Buka menu navigasi</span>
                </button>

                @auth
                    <a href="{{ route('profil') }}"
                        class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-line bg-surface text-fg transition hover:bg-surface-soft lg:hidden"
                        aria-label="Profil saya">
                        <span
                            class="flex h-7 w-7 items-center justify-center rounded-full bg-surface-soft text-xs font-bold text-fg-secondary">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                    </a>
                @endauth
            </div>
        </div>

        {{-- Panel navigasi mobile --}}
        <div id="mobile-nav" data-mobile-menu class="hidden border-t border-line bg-surface lg:hidden">
            <div class="mx-auto max-w-7xl space-y-4 px-4 py-4 sm:px-6">
                <form action="{{ route('products') }}" method="GET" role="search">
                    <label for="mobile-search" class="sr-only">Cari produk</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-fg-muted">
                            <x-ui.icon name="search" class="h-4.5 w-4.5" />
                        </span>
                        <input type="search" id="mobile-search" name="search" value="{{ request('search') }}"
                            placeholder="Cari produk..."
                            class="h-11 w-full rounded-full border border-line-strong bg-surface-muted pl-10 pr-4 text-sm text-fg placeholder:text-fg-muted">
                    </div>
                </form>

                <nav class="grid gap-1" aria-label="Navigasi mobile">
                    <a href="{{ route('home') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium {{ request()->is('/') ? 'bg-surface-soft text-fg' : 'text-fg-secondary' }}">
                        <x-ui.icon name="home" class="h-5 w-5" /> Beranda
                    </a>
                    <a href="{{ route('products') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium {{ request()->is('produk*') ? 'bg-surface-soft text-fg' : 'text-fg-secondary' }}">
                        <x-ui.icon name="package" class="h-5 w-5" /> Produk
                    </a>
                    <a href="{{ route('articles.index') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium {{ request()->is('artikel*') ? 'bg-surface-soft text-fg' : 'text-fg-secondary' }}">
                        <x-ui.icon name="sparkles" class="h-5 w-5" /> Artikel
                    </a>
                    <a href="{{ route('kontak') }}"
                        class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium {{ request()->is('kontak*') ? 'bg-surface-soft text-fg' : 'text-fg-secondary' }}">
                        <x-ui.icon name="chat" class="h-5 w-5" /> Kontak
                    </a>
                </nav>

                <div class="border-t border-line pt-3">
                    @auth
                        <div class="grid gap-1">
                            <a href="{{ route('profil') }}"
                                class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-fg-secondary">
                                <x-ui.icon name="user" class="h-5 w-5" /> Profil Saya
                            </a>
                            <a href="{{ route('orders') }}"
                                class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-fg-secondary">
                                <x-ui.icon name="package" class="h-5 w-5" /> Pesanan Saya
                            </a>
                            @if (Auth::user()->role === 'admin')
                                <a href="{{ url('/admin') }}"
                                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-fg-secondary">
                                    <x-ui.icon name="shield" class="h-5 w-5" /> Panel Admin
                                </a>
                            @else
                                <a href="{{ route('register.store.form') }}"
                                    class="flex items-center gap-3 rounded-lg px-3 py-3 text-sm font-medium text-fg-secondary">
                                    <x-ui.icon name="shield" class="h-5 w-5" /> Buat Toko
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="flex w-full items-center gap-3 rounded-lg px-3 py-3 text-left text-sm font-medium text-danger">
                                    <x-ui.icon name="arrow-right" class="h-5 w-5" /> Keluar
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="grid gap-2">
                            <a href="{{ route('login.form') }}"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg">Masuk</a>
                            <a href="{{ route('register.form') }}"
                                class="inline-flex min-h-11 w-full items-center justify-center rounded-field bg-cta px-4 text-sm font-semibold text-on-cta">Daftar
                                Akun</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    @if (session('success') || session('error') || session('warning'))
        <div class="mx-auto mt-4 max-w-7xl px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <x-ui.alert variant="success" dismissible>{{ session('success') }}</x-ui.alert>
            @endif
            @if (session('error'))
                <x-ui.alert variant="danger" dismissible class="mt-2">{{ session('error') }}</x-ui.alert>
            @endif
            @if (session('warning'))
                <x-ui.alert variant="warning" dismissible class="mt-2">{{ session('warning') }}</x-ui.alert>
            @endif
        </div>
    @endif

    <main id="main-content" tabindex="-1" class="iw-fade-in min-h-[60vh] scroll-mt-20">
        @yield('content')
    </main>

    <footer class="mt-16 border-t border-line bg-inverse text-fg-inverse">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid gap-10 md:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="360"
                            height="364" class="iw-brand-mark h-11 w-11" decoding="async">
                        <x-brand-wordmark variant="inverse" size="lg" />
                        {{-- @if ($store)
                            @if ($store->logo)
                                <img src="{{ asset('storage/' . $store->logo) }}" alt=""
                                    class="iw-brand-mark h-11 w-11 rounded-xl border border-white/15 object-cover">
                            @else
                                <img src="{{ asset('images/instanweb-logo.png') }}" alt="" width="560"
                                    height="564" class="iw-brand-mark h-11 w-11" decoding="async">
                            @endif
                            <div>
                                <p class="text-lg font-bold">{{ $storeName }}</p>
                                <p class="mt-1 flex items-center gap-1.5 text-xs text-white/60">
                                    <span>Ditenagai</span>
                                    <x-brand-wordmark variant="inverse" size="xs" />
                                </p>
                            </div>
                        @else
                            <x-brand variant="inverse" size="lg" />
                        @endif --}}
                    </div>

                    <p class="mt-4 max-w-md text-sm leading-relaxed text-white/70">
                        {{ $store->description ?? 'Temukan produk pilihan dari usaha kecil di berbagai daerah Indonesia.' }}
                    </p>

                    @if ($store && $store->address)
                        <p class="mt-3 flex items-start gap-2 text-sm text-white/60">
                            <x-ui.icon name="home" class="mt-0.5 h-4 w-4" />
                            <span>{{ $store->address }}</span>
                        </p>
                    @endif

                    @if ($whatsappNumber)
                        <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer"
                            class="mt-4 inline-flex min-h-11 items-center gap-2 rounded-field bg-white/10 px-4 text-sm font-semibold text-white transition hover:bg-white/20">
                            <x-ui.icon name="chat" class="h-4 w-4" /> Chat via WhatsApp
                        </a>
                    @endif
                </div>

                <nav aria-label="Navigasi footer">
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-white/80">Jelajahi</h2>
                    <ul class="mt-4 space-y-2 text-sm text-white/70">
                        <li><a href="{{ route('home') }}" class="transition hover:text-white">Beranda</a></li>
                        <li><a href="{{ route('products') }}" class="transition hover:text-white">Semua Produk</a>
                        </li>
                        <li><a href="{{ route('articles.index') }}" class="transition hover:text-white">Artikel</a>
                        </li>
                        <li><a href="{{ route('kontak') }}" class="transition hover:text-white">Kontak</a></li>
                        <li><a href="{{ route('home') }}" class="transition hover:text-white">Mulai di instanweb</a>
                        </li>
                    </ul>
                </nav>

                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wider text-white/80">Akun</h2>
                    <ul class="mt-4 space-y-2 text-sm text-white/70">
                        @auth
                            <li><a href="{{ route('profil') }}" class="transition hover:text-white">Profil</a></li>
                            <li><a href="{{ route('orders') }}" class="transition hover:text-white">Pesanan</a></li>
                        @else
                            <li><a href="{{ route('login.form') }}" class="transition hover:text-white">Masuk</a></li>
                            <li><a href="{{ route('register.form') }}" class="transition hover:text-white">Daftar</a>
                            </li>
                        @endauth
                        <li><a href="{{ route('cart') }}" class="transition hover:text-white">Keranjang</a></li>
                        @if ($store && $store->shopee)
                            <li><a href="{{ $store->shopee }}" target="_blank" rel="noopener noreferrer"
                                    class="transition hover:text-white">Toko Shopee</a></li>
                        @endif
                        @if ($store && $store->tokopedia)
                            <li><a href="{{ $store->tokopedia }}" target="_blank" rel="noopener noreferrer"
                                    class="transition hover:text-white">Toko Tokopedia</a></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div
                class="mt-10 flex flex-col gap-4 border-t border-white/10 pt-6 text-xs text-white/50 sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }}. Seluruh hak cipta dilindungi.</p>
                <div class="flex flex-wrap items-center gap-4">
                    @if ($store && $store->instagram)
                        <a href="https://instagram.com/{{ ltrim($store->instagram, '@') }}" target="_blank"
                            rel="noopener noreferrer" class="transition hover:text-white">Instagram</a>
                    @endif
                    @if ($store && $store->tiktok)
                        <a href="https://www.tiktok.com/@{{ ltrim($store - > tiktok, '@') }}" target="_blank"
                            rel="noopener noreferrer" class="transition hover:text-white">TikTok</a>
                    @endif
                    @if ($store && $store->facebook)
                        <a href="{{ $store->facebook }}" target="_blank" rel="noopener noreferrer"
                            class="transition hover:text-white">Facebook</a>
                    @endif
                    <a href="{{ url('/admin') }}" class="transition hover:text-white">Panel Admin</a>
                </div>
            </div>
        </div>
    </footer>

    {{-- Dialog konfirmasi global (pengganti confirm() bawaan browser). --}}
    <dialog data-confirm-dialog aria-labelledby="confirm-title"
        class="m-auto w-[min(92vw,26rem)] rounded-panel border border-line bg-surface p-6 text-fg shadow-lg backdrop:bg-black/50 backdrop:backdrop-blur-sm">
        <h2 id="confirm-title" class="text-lg font-semibold text-fg">Konfirmasi</h2>
        <p data-confirm-message class="mt-2 text-sm leading-relaxed text-fg-secondary">Apakah Anda yakin?</p>
        <div class="mt-6 flex justify-end gap-3">
            <button type="button" data-confirm-cancel
                class="inline-flex min-h-11 items-center justify-center rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">Batal</button>
            <button type="button" data-confirm-accept
                class="inline-flex min-h-11 items-center justify-center rounded-field bg-danger-strong px-4 text-sm font-semibold text-white transition hover:opacity-90">Ya,
                lanjutkan</button>
        </div>
    </dialog>

    <p class="sr-only" role="status" aria-live="polite" data-copy-feedback></p>

    @stack('scripts')
</body>

</html>
