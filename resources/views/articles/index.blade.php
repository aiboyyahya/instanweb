@extends('layouts.app')

@section('title', 'Artikel | ' . (store()->store_name ?? 'instanweb'))

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-12">
        <x-ui.page-header eyebrow="Wawasan" title="Artikel"
            description="Temukan artikel informatif dan terbaru yang telah kami sajikan untuk menambah wawasan Anda.">
            <x-ui.button :href="route('products')" variant="outline">
                <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Lihat Produk
            </x-ui.button>
        </x-ui.page-header>

        @if ($articles->isEmpty())
            <x-ui.empty-state class="mt-8" title="Belum ada artikel" icon="sparkles"
                description="Artikel akan tampil di sini setelah redaksi menambahkannya.">
                <x-ui.button :href="route('home')" variant="outline">Kembali ke Beranda</x-ui.button>
            </x-ui.empty-state>
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($articles as $article)
                    <article
                        class="group flex flex-col overflow-hidden rounded-panel border border-line bg-surface shadow-sm transition hover:shadow-md">
                        <a href="{{ route('articles.show', $article->slug) }}" class="block overflow-hidden bg-surface-soft"
                            tabindex="-1" aria-hidden="true">
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
                                <a href="{{ route('articles.show', $article->slug) }}"
                                    class="rounded transition hover:text-info">
                                    {{ $article->title }}
                                </a>
                            </h2>

                            @if ($article->description)
                                <p class="mt-2 line-clamp-3 text-sm leading-relaxed text-fg-secondary">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($article->description), 140) }}
                                </p>
                            @endif

                            <a href="{{ route('articles.show', $article->slug) }}"
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
