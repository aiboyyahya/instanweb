@extends('layouts.app')

@section('title', 'Kontak | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    @php
        $contactStore = store();
        $contactWhatsapp = $contactStore && ! empty($contactStore->whatsapp)
            ? preg_replace('/[^0-9]/', '', $contactStore->whatsapp)
            : null;
    @endphp

    <div class="mx-auto max-w-5xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => route('home')],
            ['label' => 'Kontak'],
        ]" />

        <x-ui.page-header class="mt-4" eyebrow="Bantuan" title="Kontak Kami"
            description="Hubungi kami untuk informasi lebih lanjut atau bantuan seputar produk dan pesanan." />

        @if ($contactStore)
            <div class="mt-8 grid gap-6 lg:grid-cols-3">
                {{-- Informasi toko --}}
                <section class="rounded-panel border border-line bg-surface p-6 shadow-sm lg:col-span-2 lg:p-8"
                    aria-labelledby="informasi-toko">
                    <div class="flex flex-col gap-5 sm:flex-row sm:items-start">
                        @if ($contactStore->logo)
                            <img src="{{ asset('storage/' . $contactStore->logo) }}"
                                alt="Logo {{ $contactStore->store_name }}"
                                class="h-20 w-20 shrink-0 rounded-2xl border border-line object-cover">
                        @else
                            <span
                                class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border border-line bg-surface-soft p-2">
                                <img src="{{ asset('images/instanweb-logo.png') }}" alt="instanweb" width="560"
                                    height="564" class="iw-brand-mark h-full w-full" decoding="async">
                            </span>
                        @endif

                        <div class="min-w-0">
                            <h2 id="informasi-toko" class="text-2xl font-bold tracking-tight text-fg">
                                {{ $contactStore->store_name ?? 'Toko kami' }}
                            </h2>

                            @if ($contactStore->description)
                                <p class="mt-2 text-sm leading-relaxed text-fg-secondary">
                                    {{ $contactStore->description }}
                                </p>
                            @endif

                            @if ($contactStore->address)
                                <p class="mt-4 flex items-start gap-2 text-sm text-fg-secondary">
                                    <x-ui.icon name="home" class="mt-0.5 h-4 w-4 text-fg-muted" />
                                    <span>{{ $contactStore->address }}</span>
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3 border-t border-line pt-6">
                        @if ($contactWhatsapp)
                            <x-ui.button :href="'https://wa.me/' . $contactWhatsapp" target="_blank"
                                rel="noopener noreferrer">
                                <x-ui.icon name="chat" class="h-4 w-4" /> Chat WhatsApp
                            </x-ui.button>
                        @endif

                        <x-ui.button :href="route('products')" variant="outline">
                            <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Lihat Produk
                        </x-ui.button>
                    </div>
                </section>

                {{-- Kanal kontak --}}
                <section class="rounded-panel border border-line bg-surface p-6 shadow-sm" aria-labelledby="detail-kontak">
                    <h2 id="detail-kontak" class="text-sm font-semibold uppercase tracking-wide text-fg-muted">
                        Detail Kontak
                    </h2>

                    <dl class="mt-4 space-y-4 text-sm">
                        @if ($contactStore->whatsapp)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">WhatsApp</dt>
                                <dd class="mt-1">
                                    <a href="https://wa.me/{{ $contactWhatsapp }}" target="_blank"
                                        rel="noopener noreferrer"
                                        class="inline-flex min-h-11 items-center gap-2 font-semibold text-info underline underline-offset-2">
                                        <x-ui.icon name="chat" class="h-4 w-4" />
                                        {{ $contactStore->whatsapp }}
                                    </a>
                                </dd>
                            </div>
                        @endif

                        @if ($contactStore->address)
                            <div>
                                <dt class="text-xs font-medium uppercase tracking-wide text-fg-muted">Alamat</dt>
                                <dd class="mt-1 leading-relaxed text-fg-secondary">{{ $contactStore->address }}</dd>
                            </div>
                        @endif

                        @unless ($contactStore->whatsapp || $contactStore->address)
                            <p class="text-fg-muted">Belum ada kanal kontak yang dicantumkan.</p>
                        @endunless
                    </dl>
                </section>

                {{-- Sosial media --}}
                <section class="rounded-panel border border-line bg-surface p-6 shadow-sm" aria-labelledby="sosial-media">
                    <h2 id="sosial-media" class="text-sm font-semibold uppercase tracking-wide text-fg-muted">Sosial Media</h2>

                    <ul class="mt-4 space-y-2 text-sm">
                        @if ($contactStore->instagram)
                            <li>
                                <a href="https://instagram.com/{{ ltrim($contactStore->instagram, '@') }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="flex min-h-11 items-center justify-between gap-3 rounded-field border border-line px-3 text-fg transition hover:bg-surface-soft">
                                    <span class="truncate">Instagram — {{ $contactStore->instagram }}</span>
                                    <x-ui.icon name="external" class="h-4 w-4 text-fg-muted" />
                                </a>
                            </li>
                        @endif

                        @if ($contactStore->tiktok)
                            <li>
                                <a href="https://www.tiktok.com/@{{ ltrim($contactStore->tiktok, '@') }}" target="_blank"
                                    rel="noopener noreferrer"
                                    class="flex min-h-11 items-center justify-between gap-3 rounded-field border border-line px-3 text-fg transition hover:bg-surface-soft">
                                    <span class="truncate">TikTok — {{ $contactStore->tiktok }}</span>
                                    <x-ui.icon name="external" class="h-4 w-4 text-fg-muted" />
                                </a>
                            </li>
                        @endif

                        @if ($contactStore->facebook)
                            <li>
                                <a href="{{ $contactStore->facebook }}" target="_blank" rel="noopener noreferrer"
                                    class="flex min-h-11 items-center justify-between gap-3 rounded-field border border-line px-3 text-fg transition hover:bg-surface-soft">
                                    <span class="truncate">Facebook</span>
                                    <x-ui.icon name="external" class="h-4 w-4 text-fg-muted" />
                                </a>
                            </li>
                        @endif

                        @unless ($contactStore->instagram || $contactStore->tiktok || $contactStore->facebook)
                            <li class="text-fg-muted">Belum ada akun sosial media yang dicantumkan.</li>
                        @endunless
                    </ul>
                </section>

                {{-- Marketplace --}}
                <section class="rounded-panel border border-line bg-surface p-6 shadow-sm" aria-labelledby="marketplace">
                    <h2 id="marketplace" class="text-sm font-semibold uppercase tracking-wide text-fg-muted">Marketplace</h2>

                    <ul class="mt-4 space-y-2 text-sm">
                        @if ($contactStore->shopee)
                            <li>
                                <a href="{{ $contactStore->shopee }}" target="_blank" rel="noopener noreferrer"
                                    class="flex min-h-11 items-center justify-between gap-3 rounded-field border border-line px-3 text-fg transition hover:bg-surface-soft">
                                    <span>Toko Shopee</span>
                                    <x-ui.icon name="external" class="h-4 w-4 text-fg-muted" />
                                </a>
                            </li>
                        @endif

                        @if ($contactStore->tokopedia)
                            <li>
                                <a href="{{ $contactStore->tokopedia }}" target="_blank" rel="noopener noreferrer"
                                    class="flex min-h-11 items-center justify-between gap-3 rounded-field border border-line px-3 text-fg transition hover:bg-surface-soft">
                                    <span>Toko Tokopedia</span>
                                    <x-ui.icon name="external" class="h-4 w-4 text-fg-muted" />
                                </a>
                            </li>
                        @endif

                        @unless ($contactStore->shopee || $contactStore->tokopedia)
                            <li class="text-fg-muted">Belum ada tautan marketplace.</li>
                        @endunless
                    </ul>
                </section>
            </div>

            <div class="mt-6 rounded-panel border border-line bg-surface-soft p-6">
                <h2 class="text-base font-semibold text-fg">Butuh bantuan pesanan?</h2>
                <p class="mt-1 text-sm leading-relaxed text-fg-secondary">
                    Sertakan nomor pesanan saat menghubungi kami agar proses pengecekan lebih cepat.
                    @auth
                        Lihat riwayat di
                        <a href="{{ route('orders') }}" class="font-semibold text-info underline underline-offset-2">Pesanan
                            Saya</a>.
                    @else
                        <a href="{{ route('login.form') }}"
                            class="font-semibold text-info underline underline-offset-2">Masuk</a> untuk melihat riwayat
                        pesanan.
                    @endauth
                </p>
            </div>
        @else
            <x-ui.empty-state class="mt-8" title="Informasi toko belum tersedia" icon="chat"
                description="Pemilik toko belum mengisi informasi kontak. Silakan kembali lagi nanti.">
                <x-ui.button :href="route('home')" variant="outline">Kembali ke Beranda</x-ui.button>
            </x-ui.empty-state>
        @endif
    </div>
@endsection
