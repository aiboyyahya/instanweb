@extends('layouts.app')

@section('title', 'Selamat Datang | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $welcomeStore = store();
        $welcomeStoreName = $welcomeStore->store_name ?? 'instanweb';
        $isAdmin = auth()->check() && auth()->user()->role === 'admin';
        $whatsappNumber = $welcomeStore && ! empty($welcomeStore->whatsapp)
            ? preg_replace('/[^0-9]/', '', $welcomeStore->whatsapp)
            : null;
    @endphp

    {{-- Hero --}}
    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-panel border border-line bg-inverse text-fg-inverse shadow-sm">
            <div class="relative grid gap-8 p-6 sm:p-10 lg:grid-cols-2 lg:items-center lg:p-14">
                <div aria-hidden="true"
                    class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-brand-sky/20 blur-3xl">
                </div>
                <div aria-hidden="true"
                    class="pointer-events-none absolute -bottom-32 left-1/3 h-72 w-72 rounded-full bg-brand/20 blur-3xl">
                </div>

                <div class="relative">
                    <p class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-3 py-1 text-xs font-semibold">
                        <x-ui.icon name="sparkles" class="h-3.5 w-3.5" />
                        Selamat datang
                    </p>

                    <h1 class="mt-4 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                        @auth
                            Halo, {{ auth()->user()->name }}.
                        @else
                            Bangun <br>Toko Online</br> Anda Sendiri
                        @endauth
                    </h1>

                    <p class="mt-4 max-w-lg text-sm leading-relaxed text-white/80 sm:text-base">
                        {{ $welcomeStoreName }} menghadirkan produk UMKM pilihan dalam satu toko online.
                        Pilih langkah yang sesuai: mulai berbelanja, kelola toko, atau hubungi kami bila butuh bantuan.
                    </p>

                    <div class="mt-7 flex flex-wrap gap-3">
                        @auth
                            <x-ui.button :href="$isAdmin ? url('/admin') : route('home')" size="lg">
                                @if ($isAdmin)
                                    <x-ui.icon name="shield" class="h-5 w-5" /> Buka Panel Admin
                                @else
                                    <x-ui.icon name="home" class="h-5 w-5" /> Lanjut ke Beranda
                                @endif
                            </x-ui.button>

                            @unless ($isAdmin)
                                <x-ui.button :href="route('orders')" variant="outline" size="lg"
                                    class="border-white/30 bg-white/10 text-white hover:bg-white/20">
                                    <x-ui.icon name="package" class="h-5 w-5" /> Pesanan Saya
                                </x-ui.button>
                            @endunless
                        @else
                            <x-ui.button :href="route('home')" size="lg">
                                <x-ui.icon name="home" class="h-5 w-5" /> Mulai Belanja
                            </x-ui.button>

                            <x-ui.button :href="route('register.store.form')" variant="outline" size="lg"
                                class="border-white/30 bg-white/10 text-white hover:bg-white/20">
                                <x-ui.icon name="shield" class="h-5 w-5" /> Buka Toko
                            </x-ui.button>
                        @endauth
                    </div>

                    @guest
                        <p class="mt-4 text-sm text-white/60">
                            Sudah punya akun?
                            <a href="{{ route('login.form') }}"
                                class="font-semibold text-white underline underline-offset-2">Masuk di sini</a>.
                            Belum punya akun?
                            <a href="{{ route('register.form') }}"
                                class="font-semibold text-white underline underline-offset-2">Daftar pembeli</a>.
                        </p>
                    @endguest
                </div>

                {{-- Ringkasan langkah --}}
                <div class="relative grid gap-3 sm:grid-cols-2">
                    <div class="rounded-card border border-white/15 bg-white/5 p-5">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                            <x-ui.icon name="shopping-bag" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-sm font-semibold">1. Pilih produk</p>
                        <p class="mt-1 text-xs leading-relaxed text-white/65">Jelajahi katalog dan masukkan produk ke
                            keranjang.</p>
                    </div>

                    <div class="rounded-card border border-white/15 bg-white/5 p-5">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                            <x-ui.icon name="truck" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-sm font-semibold">2. Isi pengiriman</p>
                        <p class="mt-1 text-xs leading-relaxed text-white/65">Lengkapi alamat, lalu konfirmasi pesanan
                            Anda.</p>
                    </div>

                    <div class="rounded-card border border-white/15 bg-white/5 p-5">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                            <x-ui.icon name="chat" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-sm font-semibold">3. Konfirmasi</p>
                        <p class="mt-1 text-xs leading-relaxed text-white/65">Hubungi penjual untuk memastikan pembayaran
                            dan pengiriman.</p>
                    </div>

                    <div class="rounded-card border border-white/15 bg-white/5 p-5">
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-white/10">
                            <x-ui.icon name="check-circle" class="h-5 w-5" />
                        </span>
                        <p class="mt-3 text-sm font-semibold">4. Beri ulasan</p>
                        <p class="mt-1 text-xs leading-relaxed text-white/65">Setelah pesanan selesai, bagikan penilaian
                            produk.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Pilihan jalur --}}
    <section class="mx-auto mt-8 max-w-7xl px-4 sm:px-6 lg:px-8" aria-labelledby="pilih-langkah">
        <h2 id="pilih-langkah" class="sr-only">Pilih langkah Anda</h2>

        <div class="grid gap-4 lg:grid-cols-3">
            {{-- Belanja --}}
            <article class="flex flex-col rounded-panel border border-line bg-surface p-6 shadow-sm">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-card bg-surface-soft text-brand-text"
                    aria-hidden="true">
                    <x-ui.icon name="shopping-bag" class="h-5 w-5" />
                </span>
                <h3 class="mt-4 text-lg font-bold text-fg">Saya ingin berbelanja</h3>
                <p class="mt-1 flex-1 text-sm leading-relaxed text-fg-secondary">
                    Lihat katalog lengkap, gunakan pencarian, dan tambahkan produk ke keranjang. Checkout memerlukan akun
                    agar pesanan dapat dilacak.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    <x-ui.button :href="route('products')" variant="outline">Lihat Produk</x-ui.button>
                    @auth
                        <x-ui.button :href="route('cart')" variant="ghost">Keranjang</x-ui.button>
                    @else
                        <x-ui.button :href="route('login.form')" variant="ghost">Masuk</x-ui.button>
                    @endauth
                </div>
            </article>

            {{-- Kelola toko --}}
            <article class="flex flex-col rounded-panel border border-line bg-surface p-6 shadow-sm">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-card bg-surface-soft text-info"
                    aria-hidden="true">
                    <x-ui.icon name="shield" class="h-5 w-5" />
                </span>
                <h3 class="mt-4 text-lg font-bold text-fg">Saya ingin mengelola toko</h3>

                @if ($isAdmin)
                    <p class="mt-1 flex-1 text-sm leading-relaxed text-fg-secondary">
                        Kelola produk, kategori, pesanan, dan konten artikel melalui panel admin Filament.
                    </p>

                    <div class="mt-5">
                        <x-ui.button :href="url('/admin')">
                            <x-ui.icon name="shield" class="h-4 w-4" /> Buka Panel Admin
                        </x-ui.button>
                    </div>
                @else
                    <p class="mt-1 flex-1 text-sm leading-relaxed text-fg-secondary">
                        Buka toko online Anda sendiri. Daftarkan akun pemilik toko, lengkapi identitas usaha, lalu kelola
                        produk dan pesanan dari satu panel.
                    </p>

                    <div class="mt-5 flex flex-wrap gap-2">
                        <x-ui.button :href="route('register.store.form')">
                            <x-ui.icon name="shield" class="h-4 w-4" /> Buat Toko
                        </x-ui.button>

                        <x-ui.button :href="route('login.form')" variant="ghost">Masuk</x-ui.button>
                    </div>

                    <p class="mt-3 text-xs leading-relaxed text-fg-muted">
                        Sudah punya akun pembeli? Buka profil Anda untuk mengubahnya menjadi akun pemilik toko.
                    </p>
                @endif
            </article>

            {{-- Bantuan --}}
            <article class="flex flex-col rounded-panel border border-line bg-surface p-6 shadow-sm">
                <span class="inline-flex h-11 w-11 items-center justify-center rounded-card bg-surface-soft text-success"
                    aria-hidden="true">
                    <x-ui.icon name="chat" class="h-5 w-5" />
                </span>
                <h3 class="mt-4 text-lg font-bold text-fg">Saya butuh bantuan</h3>
                <p class="mt-1 flex-1 text-sm leading-relaxed text-fg-secondary">
                    Tanyakan ketersediaan produk, status pesanan, atau cara pembayaran langsung kepada penjual.
                </p>

                <div class="mt-5 flex flex-wrap gap-2">
                    @if ($whatsappNumber)
                        <x-ui.button :href="'https://wa.me/' . $whatsappNumber" target="_blank" rel="noopener noreferrer">
                            <x-ui.icon name="chat" class="h-4 w-4" /> WhatsApp
                        </x-ui.button>
                    @endif

                    <x-ui.button :href="route('kontak')" variant="outline">Halaman Kontak</x-ui.button>
                </div>
            </article>
        </div>
    </section>

    {{-- Catatan transparan --}}
    <section class="mx-auto mt-8 max-w-7xl px-4 pb-4 sm:px-6 lg:px-8">
        <div class="rounded-panel border border-line bg-surface-soft p-6">
            <h2 class="text-base font-semibold text-fg">Tentang {{ $welcomeStoreName }}</h2>
            <p class="mt-1 text-sm leading-relaxed text-fg-secondary">
                Ini adalah toko online dari satu penjual. Produk, harga, dan ketersediaan mengikuti data yang
                dikelola penjual. Untuk pertanyaan sebelum membeli, gunakan kanal kontak toko agar mendapat jawaban
                langsung.
            </p>
        </div>
    </section>
@endsection
