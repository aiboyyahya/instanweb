@extends('layouts.app')

@php
    $articlesUrl = $store ? route('store.articles.index', $store) : route('articles.index');
    $articleUrl = fn($article) => $store
        ? route('store.articles.show', [$store, $article])
        : route('articles.show', $article);
    $productsUrl = $store ? route('store.products', $store) : route('products');
@endphp

@section('title', ($store->store_name ?? 'Artikel') . ' | ' . config('app.name', 'instanweb'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-ui.button :href="route('home')" variant="ghost">
            <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke Beranda
        </x-ui.button>
    </section>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-8">
        <x-ui.page-header eyebrow="Wawasan" :title="$store ? 'Artikel ' . $store->store_name : 'Artikel'"
            description="{{ $store ? 'Informasi, tips, dan cerita terbaru dari ' . $store->store_name . '.' : 'Temukan artikel informatif dan terbaru yang telah kami sajikan untuk menambah wawasan Anda.' }}">
            <x-ui.button :href="$productsUrl" variant="outline">
                <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Lihat Produk
            </x-ui.button>
        </x-ui.page-header>

        @if ($articles->isEmpty())
            <x-ui.empty-state class="mt-8" title="Belum ada artikel" icon="sparkles"
                description="Artikel akan tampil di sini setelah pemilik toko menambahkannya.">
                <x-ui.button :href="$store ? route('store.show', $store) : route('home')" variant="outline">
                    {{ $store ? 'Kembali ke Toko' : 'Kembali ke Beranda' }}
                </x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article
                        class="group flex flex-col overflow-hidden rounded-panel border border-line bg-surface shadow-sm transition hover:shadow-md">
                        <a href="{{ $articleUrl($article) }}" class="block overflow-hidden bg-surface-soft" tabindex="-1"
                            aria-hidden="true">
                            @if ($article->image)
                                <img src="{{ asset('storage/' . $article->image) }}" alt=""
                                    class="h-48 w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                                    loading="lazy">
                            @else
                                <span class="flex h-48 w-full items-center justify-center text-fg-muted">
                                    <x-ui.icon name="sparkles" class="h-10 w-10" />
                                </span>
                            @endif
                        </a>

                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                            <p class="text-xs font-medium text-fg-muted">
                                <time datetime="{{ $article->created_at->toIso8601String() }}">
                                    {{ $article->created_at->translatedFormat('d M Y') }}
                                </time>
                                <span aria-hidden="true">·</span>
                                {{ $article->user?->name ?? 'Redaksi' }}
                            </p>

                            <h2 class="mt-2 text-lg font-bold leading-snug text-fg">
                                <a href="{{ $articleUrl($article) }}" class="rounded transition hover:text-info">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            @if ($article->description)
                                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-fg-secondary">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($article->description), 140) }}
                                </p>
                            @endif

                            <a href="{{ $articleUrl($article) }}"
                                class="mt-4 inline-flex min-h-11 items-center gap-1.5 self-start text-sm font-semibold text-info">
                                Baca selengkapnya
                                <x-ui.icon name="arrow-right" class="h-4 w-4" />
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <x-ui.pagination :paginator="$articles" class="mt-8" />
        @endif
    </div>
@endsection
