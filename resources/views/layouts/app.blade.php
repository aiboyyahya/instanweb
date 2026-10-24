<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'InstanWeb | Pasar Produk UMKM Indonesia')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            color-scheme: dark;
            --iw-bg: #0b1120;
            --iw-panel: #111c2f;
            --iw-panel-soft: #16243a;
            --iw-border: #26364d;
            --iw-text: #e8eef7;
            --iw-muted: #9aabc2;
            --iw-blue: #38bdf8;
            --iw-orange: #fb923c;
        }
        html { background: var(--iw-bg); scroll-behavior: smooth; }
        body { background: var(--iw-bg) !important; color: var(--iw-text) !important; }
        nav { background: rgba(11,17,32,.96) !important; color: var(--iw-text) !important; border-color: var(--iw-border) !important; backdrop-filter: blur(14px); }
        nav a, footer a { transition: color .2s ease, background .2s ease, transform .2s ease; }
        nav a:hover { color: var(--iw-blue) !important; }
        footer { background: #070c16 !important; border-color: var(--iw-border) !important; }
        section, main { background-color: var(--iw-bg) !important; color: var(--iw-text) !important; }
        .bg-white, .bg-gray-50, .bg-gray-100, .bg-slate-50, .bg-slate-100 {
            background-color: var(--iw-panel) !important;
        }
        .bg-gray-200, .bg-slate-200 { background-color: var(--iw-panel-soft) !important; }
        .text-gray-900, .text-gray-800, .text-slate-900, .text-slate-800, .text-black {
            color: var(--iw-text) !important;
        }
        .text-gray-700, .text-slate-700 { color: #cbd7e7 !important; }
        .text-gray-600, .text-gray-500, .text-gray-400, .text-slate-600, .text-slate-500 {
            color: var(--iw-muted) !important;
        }
        .border-gray-100, .border-gray-200, .border-gray-300, .border-slate-100, .border-slate-200, .border-gray-600 {
            border-color: var(--iw-border) !important;
        }
        .divide-gray-100 > :not([hidden]) ~ :not([hidden]),
        .divide-gray-200 > :not([hidden]) ~ :not([hidden]) { border-color: var(--iw-border) !important; }
        input:not([type="checkbox"]):not([type="radio"]):not([type="hidden"]),
        select, textarea {
            background: #0b1424 !important;
            color: var(--iw-text) !important;
            border-color: #344761 !important;
            border-radius: .8rem;
        }
        input::placeholder, textarea::placeholder { color: #8193ad !important; }
        input:focus, select:focus, textarea:focus {
            outline: none !important;
            border-color: var(--iw-blue) !important;
            box-shadow: 0 0 0 3px rgba(56,189,248,.14) !important;
        }
        .shadow, .shadow-sm, .shadow-md, .shadow-lg, .shadow-xl, .shadow-2xl {
            --tw-shadow: 0 12px 30px rgba(0,0,0,.18) !important;
            box-shadow: 0 12px 30px rgba(0,0,0,.18) !important;
        }
        .rounded-xl, .rounded-2xl, .rounded-3xl { border-color: var(--iw-border); }
        .bg-blue-600, .bg-blue-700, .bg-indigo-600 { background-color: #087eaa !important; }
        .hover\:bg-blue-700:hover, .hover\:bg-indigo-700:hover { background-color: #0b9acb !important; }
        .text-blue-600, .text-blue-700, .text-indigo-600, .text-indigo-700 { color: var(--iw-blue) !important; }
        .bg-orange-500, .bg-orange-600 { background-color: #ea741c !important; }
        .hover\:bg-orange-600:hover { background-color: #fb923c !important; }
        .bg-green-50, .bg-green-100 { background-color: #102c2a !important; }
        .text-green-700, .text-green-800 { color: #6ee7b7 !important; }
        .border-green-200, .border-green-400 { border-color: #1e6254 !important; }
        .bg-red-50, .bg-red-100 { background-color: #351b28 !important; }
        .text-red-700, .text-red-800 { color: #fda4af !important; }
        .border-red-200, .border-red-400 { border-color: #783447 !important; }
        button, [type="submit"] { transition: transform .18s ease, filter .18s ease, box-shadow .18s ease; }
        button:hover, [type="submit"]:hover { filter: brightness(1.12); }
        button:active, [type="submit"]:active { transform: scale(.985); }
        img { max-width: 100%; }
        @media (max-width: 640px) {
            nav { padding-top: .8rem !important; padding-bottom: .8rem !important; }
            h1 { line-height: 1.15 !important; }
        }
    </style>

</head>

<body class="bg-white text-gray-800">

    <nav class="px-4 py-3 shadow-sm border-b relative bg-white">
        <input type="checkbox" id="menu-toggle" class="peer hidden">
        <div class="flex items-center justify-between w-full">
            <a href="{{ url('/') }}" class="flex items-center gap-3" aria-label="InstanWeb Beranda">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-700 text-sm font-extrabold text-white shadow-sm">IW</span>
                <span class="flex flex-col">
                    <span class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900">InstanWeb</span>
                    <span class="hidden text-xs text-slate-500 sm:block">Pasar Produk UMKM Indonesia</span>
                </span>
            </a>

            <div class="hidden md:flex space-x-4 items-center font-medium text-sm">
                <a href="{{ url('/') }}"
                    class="pb-1 {{ request()->is('/') ? 'text-black font-semibold border-b-2 border-black' : 'hover:text-gray-900' }} transition">
                    Beranda
                </a>
                <a href="{{ url('/produk') }}"
                    class="pb-1 {{ request()->is('produk*') ? 'text-black font-semibold border-b-2 border-black' : 'hover:text-gray-900' }} transition">
                    Produk
                </a>
                <a href="{{ route('articles.index') }}"
                    class="pb-1 {{ request()->is('artikel*') ? 'text-black font-semibold border-b-2 border-black' : 'hover:text-gray-900' }} transition">
                    Artikel
                </a>
                <a href="{{ route('kontak') }}"
                    class="pb-1 {{ request()->is('kontak') ? 'text-black font-semibold border-b-2 border-black' : 'hover:text-gray-900' }} transition">
                    Kontak
                </a>

                <form action="{{ url('/produk') }}" method="GET" class="relative" id="navbarSearchForm">
                    <input type="text" name="q" id="navbarSearchInput" placeholder="Cari produk..."
                        value="{{ request()->query('q') }}"
                        class="border border-gray-300 rounded-full pl-3 pr-10 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <button type="submit"
                        class="absolute right-1 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1112 4.5a7.5 7.5 0 014.65 12.15z" />
                        </svg>
                    </button>
                </form>

                <script>
                    document.getElementById('navbarSearchForm').addEventListener('submit', function(e) {
                        e.preventDefault();
                        var query = document.getElementById('navbarSearchInput').value.trim();
                        if (query.length === 0) {
                            window.location.href = '{{ url('/') }}';
                        } else {
                            window.location.href = '{{ url('/produk') }}' + '?search=' + encodeURIComponent(query);
                        }
                    });
                </script>
            </div>

            <div class="flex items-center space-x-4 text-sm">
                @auth
                    <div class="relative group hidden md:block">
                        <button
                            class="flex items-center space-x-2 px-2 py-1 rounded-full hover:bg-gray-100 transition font-medium">
                            @if (Auth::user()->profile_photo)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo) . '?v=' . time() }}"
                                    alt="{{ Auth::user()->name }}"
                                    class="w-8 h-8 rounded-full object-cover border border-gray-300">
                            @else
                                <div
                                    class="w-8 h-8 bg-gray-300 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            class="absolute right-0 mt-2 w-44 bg-white border border-gray-200 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 invisible group-hover:visible transition-all z-50">
                            <a href="{{ route('profil') }}" class="block px-4 py-2 hover:bg-gray-100 transition">Profil</a>
                            <a href="{{ url('/pesanan') }}"
                                class="block px-4 py-2 hover:bg-gray-100 transition">Pesanan</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="w-full text-left px-4 py-2 hover:bg-gray-100 transition">Logout</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login.form') }}"
                        class="px-4 py-2 bg-white border border-black rounded-full hover:bg-black hover:text-white transition font-medium">
                        Login
                    </a>
                @endauth

                <a href="{{ route('cart') }}" class="relative text-xl cursor-pointer hover:text-gray-700 transition">
                    🛒
                    @php
                        $cart = session()->get('cart', []);
                        $cartCount = count($cart);
                    @endphp
                    @if ($cartCount > 0)
                        <span
                            class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                            {{ $cartCount }}
                        </span>
                    @endif
                </a>

                <label for="menu-toggle" class="md:hidden cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </label>
            </div>
        </div>

        <div class="flex-col space-y-1 text-sm font-medium mt-4 hidden peer-checked:flex md:hidden border-t pt-3">
            <a href="{{ url('/') }}" class="py-2 text-center hover:bg-gray-50 transition">Beranda</a>
            <a href="{{ url('/produk') }}" class="py-2 text-center hover:bg-gray-50 transition">Produk</a>
            <a href="{{ route('articles.index') }}" class="py-2 text-center hover:bg-gray-50 transition">Artikel</a>
            <a href="{{ route('kontak') }}" class="py-2 text-center hover:bg-gray-50 transition">Kontak</a>

            @auth
                <a href="{{ route('profil') }}" class="py-2 text-center hover:bg-gray-50 transition">Profil</a>
                <a href="{{ url('/pesanan') }}" class="py-2 text-center hover:bg-gray-50 transition">Pesanan</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="w-full py-2 text-center hover:bg-gray-50 transition">Logout</button>
                </form>
            @else
                <a href="{{ route('login.form') }}"
                    class="px-4 py-2 mt-1 bg-white border border-black rounded-full hover:bg-black hover:text-white transition font-medium text-center w-fit mx-auto">
                    Login
                </a>
            @endauth
        </div>
    </nav>

    <main class="min-h-screen">
        @yield('content')
    </main>

    <footer class="bg-black text-white py-8 border-t border-gray-800 text-sm">
        @if (store())
            <div class="max-w-6xl mx-auto text-center">

                <div class="flex flex-wrap justify-center items-center space-x-6 mb-4">

                    @if (!empty(store()->instagram))
                        <a href="https://instagram.com/{{ ltrim(store()->instagram, '@') }}" target="_blank"
                            class="flex items-center space-x-2 hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M7 2C4.243 2 2 4.243 2 7v10c0 2.757 2.243 5 5 5h10c2.757 0 5-2.243 5-5V7c0-2.757-2.243-5-5-5H7zm10 2c1.654 0 3 1.346 3 3v10c0 1.654-1.346 3-3 3H7c-1.654 0-3-1.346-3-3V7c0-1.654 1.346-3 3-3h10zM12 7a5 5 0 100 10 5 5 0 000-10zm0 2a3 3 0 110 6 3 3 0 010-6zm4.5-3a1.5 1.5 0 11-3-.001 1.5 1.5 0 013 .001z" />
                            </svg>
                            <span>{{ store()->instagram }}</span>
                        </a>
                    @endif

                    @if (!empty(store()->tiktok))
                        <a href="https://www.tiktok.com/@{{ ltrim(store() - > tiktok, '@') }}" target="_blank"
                            class="flex items-center space-x-2 hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5"
                                fill="currentColor">
                                <path
                                    d="M16.5 3c.2 1.3 1.1 2.3 2.3 2.6.3.1.6.1.9.2v2.3c-.4 0-.8-.1-1.2-.2-.5-.1-1-.3-1.5-.6v6.6c0 3-2.2 5.7-5.3 6-3.3.4-6.2-2-6.6-5.2-.4-3.3 2-6.2 5.2-6.6.4-.1.9-.1 1.3 0v2.4c-.3-.1-.6-.1-.9 0-1.6.2-2.8 1.7-2.6 3.4.2 1.6 1.7 2.8 3.4 2.6 1.5-.2 2.6-1.5 2.6-3V3h3.4z" />
                            </svg>
                            <span>{{ store()->tiktok }}</span>
                        </a>
                    @endif

                    @if (!empty(store()->facebook))
                        <a href="{{ store()->facebook }}" target="_blank"
                            class="flex items-center space-x-2 hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.988H8.078V12h2.36V9.797c0-2.332 1.393-3.622 3.522-3.622.999 0 2.043.177 2.043.177v2.248h-1.151c-1.136 0-1.493.704-1.493 1.425V12h2.543l-.406 2.89h-2.137v6.988C18.343 21.128 22 16.991 22 12z" />
                            </svg>
                            <span>{{ store()->facebook }}</span>
                        </a>
                    @endif

                    @if (!empty(store()->whatsapp))
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', store()->whatsapp) }}" target="_blank"
                            class="flex items-center space-x-2 hover:text-gray-300 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                viewBox="0 0 24 24">
                                <path
                                    d="M12 2a10 10 0 00-8.94 14.36L2 22l5.78-1.9A10 10 0 1012 2zm0 18a8 8 0 01-4.28-1.22l-.31-.2-3.43 1.13 1.14-3.27-.21-.32A8 8 0 1112 20zm4.26-5.47c-.23-.12-1.37-.68-1.58-.76s-.37-.12-.53.12-.62.76-.76.92-.28.17-.51.05a6.54 6.54 0 01-1.92-1.19 7.12 7.12 0 01-1.31-1.62c-.14-.23 0-.35.1-.47s.23-.28.35-.42a1.56 1.56 0 00.23-.39.43.43 0 00-.02-.41c-.06-.12-.53-1.27-.73-1.74s-.39-.4-.53-.41h-.45a.86.86 0 00-.62.29A2.62 2.62 0 007 9.83 4.57 4.57 0 007.92 12a10.62 10.62 0 003.61 3.61 7.87 7.87 0 002.47 1c.26.04.5.03.68-.02a2.1 2.1 0 001.37-1.1 1.72 1.72 0 00.12-1c-.05-.09-.21-.15-.41-.25z" />
                            </svg>
                            <span>{{ store()->whatsapp }}</span>
                        </a>
                    @endif
                </div>

                <div class="flex justify-center space-x-6 mb-2">
                    @if (!empty(store()->shopee))
                        <a href="{{ store()->shopee }}" target="_blank"
                            class="hover:text-gray-300 transition">Shopee</a>
                    @endif
                    @if (!empty(store()->tokopedia))
                        <a href="{{ store()->tokopedia }}" target="_blank"
                            class="hover:text-gray-300 transition">Tokopedia</a>
                    @endif
                </div>
            </div>
        @endif

        <div class="text-center mt-4 text-gray-400">
            &copy; {{ date('Y') }} InstanWeb — Pasar Produk UMKM Indonesia. All rights reserved.
        </div>
    </footer>

</body>

</html>
