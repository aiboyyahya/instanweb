@extends('layouts.app')

@php
    $articleListUrl = $store ? route('store.articles.index', $store) : route('articles.index');
    $productsUrl = $store ? route('store.products', $store) : route('products');
    $homeUrl = $store ? route('store.show', $store) : route('home');
@endphp

@section('title', $article->title . ' | ' . ($store->store_name ?? config('app.name', 'instanweb')))

@section('content')
    @php
        $shareUrl = urlencode(request()->fullUrl());
        $shareTitle = urlencode($article->title);
        $heroImage = $article->image ?: $article->thumbnail;
        $authorName = $article->user?->name ?? 'Redaksi';
    @endphp

    <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.breadcrumb :items="[
            ['label' => 'Beranda', 'url' => $homeUrl],
            ['label' => 'Artikel', 'url' => $articleListUrl],
            ['label' => \Illuminate\Support\Str::limit($article->title, 40)],
        ]" />

        <article class="mt-6">
            <header class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-brand-text">Artikel</p>
                <h1 class="mt-2 text-3xl font-extrabold leading-tight tracking-tight text-fg sm:text-4xl lg:text-5xl">
                    {{ $article->title }}
                </h1>
                <p class="mt-3 text-base leading-relaxed text-fg-muted">
                    Informasi inspiratif &amp; edukatif untuk membantu pengalaman belanja lebih cerdas.
                </p>

                <div class="mt-6 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-sm text-fg-muted">
                    <span class="flex items-center gap-2">
                        @if ($article->user?->profile_photo)
                            <img src="{{ asset('storage/' . $article->user->profile_photo) }}" alt=""
                                class="h-9 w-9 rounded-full object-cover ring-1 ring-line">
                        @else
                            <span
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-surface-soft text-xs font-bold text-fg-secondary ring-1 ring-line">{{ strtoupper(substr($authorName, 0, 1)) }}</span>
                        @endif
                        <span class="font-medium text-fg">{{ $authorName }}</span>
                    </span>

                    <span aria-hidden="true">·</span>

                    <time datetime="{{ $article->created_at->toIso8601String() }}">
                        {{ $article->created_at->translatedFormat('d F Y') }}
                    </time>
                </div>
            </header>

            @if ($heroImage)
                <figure class="mx-auto mt-8 max-w-5xl overflow-hidden rounded-panel border border-line bg-surface-soft">
                    <img src="{{ asset('storage/' . $heroImage) }}" alt="{{ $article->title }}"
                        class="max-h-[460px] w-full object-cover" fetchpriority="high">
                </figure>
            @endif

            <div class="mx-auto mt-10 grid max-w-5xl gap-8 lg:grid-cols-[1fr_280px]">
                <div class="min-w-0 rounded-panel border border-line bg-surface p-6 shadow-sm sm:p-8">
                    <div class="article-prose">
                        {!! $article->description !!}
                    </div>
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24 lg:self-start" aria-label="Bagikan artikel">
                    <div class="rounded-panel border border-line bg-surface p-5 shadow-sm">
                        <h2 class="text-sm font-semibold uppercase tracking-wide text-fg-muted">Bagikan Artikel</h2>

                        <div class="mt-4 grid gap-2">
                            <a href="https://wa.me/?text={{ $shareTitle }}%20{{ $shareUrl }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field bg-success-strong px-4 text-sm font-semibold text-white transition hover:opacity-90">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M12 2a10 10 0 00-8.94 14.36L2 22l5.78-1.9A10 10 0 1012 2zm0 18a8 8 0 01-4.28-1.22l-.31-.2-3.43 1.13 1.14-3.27-.21-.32A8 8 0 1112 20zm4.26-5.47c-.23-.12-1.37-.68-1.58-.76s-.37-.12-.53.12-.62.76-.76.92-.28.17-.51.05a6.54 6.54 0 01-1.92-1.19 7.12 7.12 0 01-1.31-1.62c-.14-.23 0-.35.1-.47s.23-.28.35-.42a1.56 1.56 0 00.23-.39.43.43 0 00-.02-.41c-.06-.12-.53-1.27-.73-1.74s-.39-.4-.53-.41h-.45a.86.86 0 00-.62.29A2.62 2.62 0 007 9.83 4.57 4.57 0 007.92 12a10.62 10.62 0 003.61 3.61 7.87 7.87 0 002.47 1c.26.04.5.03.68-.02a2.1 2.1 0 001.37-1.1 1.72 1.72 0 00.12-1c-.05-.09-.21-.15-.41-.25z" />
                                </svg>
                                WhatsApp
                            </a>

                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info" fill="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.988H8.078V12h2.36V9.797c0-2.332 1.393-3.622 3.522-3.622.999 0 2.043.177 2.043.177v2.248h-1.151c-1.136 0-1.493.704-1.493 1.425V12h2.543l-.406 2.89h-2.137v6.988C18.343 21.128 22 16.991 22 12z" />
                                </svg>
                                Facebook
                            </a>

                            <a href="https://twitter.com/intent/tweet?url={{ $shareUrl }}&text={{ $shareTitle }}"
                                target="_blank" rel="noopener noreferrer"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-info" fill="currentColor"
                                    viewBox="0 0 24 24" aria-hidden="true">
                                    <path
                                        d="M24 4.557a9.83 9.83 0 01-2.828.775A4.932 4.932 0 0023.337 3.1a9.864 9.864 0 01-3.127 1.195A4.916 4.916 0 0016.616 3c-2.733 0-4.95 2.216-4.95 4.95 0 .388.045.765.13 1.13C7.728 8.94 4.1 6.897 1.671 3.882a4.92 4.92 0 00-.67 2.49c0 1.72.875 3.236 2.207 4.125a4.903 4.903 0 01-2.24-.618v.063c0 2.403 1.71 4.405 3.977 4.864a4.996 4.996 0 01-2.224.085c.627 1.956 2.445 3.379 4.6 3.418a9.869 9.869 0 01-6.102 2.105c-.398 0-.79-.024-1.175-.07A13.93 13.93 0 007.548 21c9.142 0 14.307-7.72 14.307-14.416 0-.22-.005-.437-.015-.653A10.326 10.326 0 0024 4.557z" />
                                </svg>
                                Twitter
                            </a>

                            <button type="button" data-copy="{{ request()->fullUrl() }}"
                                class="inline-flex min-h-11 items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg transition hover:bg-surface-soft">
                                Salin tautan
                            </button>
                        </div>
                    </div>

                    <x-ui.button :href="$articleListUrl" variant="outline" block>
                        <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke Artikel
                    </x-ui.button>
                </aside>
            </div>
        </article>

        <div class="mx-auto mt-10 flex max-w-5xl flex-wrap items-center justify-between gap-4 border-t border-line pt-6">
            <p class="text-sm text-fg-muted">Butuh produk untuk dicoba?</p>
            <x-ui.button :href="$productsUrl" variant="outline">
                Jelajahi Produk <x-ui.icon name="arrow-right" class="h-4 w-4" />
            </x-ui.button>
        </div>
    </div>
@endsection
