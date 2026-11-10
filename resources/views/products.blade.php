@extends('layouts.app')

@php
    $activeStore = $store ?? null;
    $productsUrl = $activeStore ? route('store.products', $activeStore) : route('products');
@endphp

@section('title', ($activeStore->store_name ?? 'Semua Produk') . ' | ' . config('app.name', 'instanweb'))

@section('content')
    <section class="mx-auto max-w-7xl px-4 pt-6 sm:px-6 lg:px-8">
        <x-ui.button :href="route('home')" variant="ghost">
            <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke Beranda
        </x-ui.button>
    </section>
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-8">
        <x-ui.page-header eyebrow="Katalog" :title="$activeStore ? 'Produk ' . $activeStore->store_name : 'Semua Produk'"
            description="{{ $activeStore ? 'Produk yang dijual oleh ' . $activeStore->store_name . '.' : 'Temukan produk terbaik dari toko-toko UMKM. Gunakan pencarian atau filter kategori untuk mempersempit pilihan.' }}">
            @if ($activeStore)
                <x-ui.button :href="route('store.show', $activeStore)" variant="outline">
                    <x-ui.icon name="arrow-left" class="h-4 w-4" /> Kembali ke Toko
                </x-ui.button>
            @endif
            <x-ui.button :href="route('cart')" variant="outline">
                <x-ui.icon name="shopping-bag" class="h-4 w-4" /> Keranjang
            </x-ui.button>
        </x-ui.page-header>

        {{-- Filter --}}
        <section class="mt-8 rounded-panel border border-line bg-surface p-4 shadow-sm sm:p-5" aria-label="Filter produk">
            <form id="filterForm" method="GET" action="{{ $productsUrl }}" role="search"
                class="flex flex-col gap-3 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="searchInput" class="mb-1.5 block text-sm font-medium text-fg-secondary">Cari produk</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-3.5 flex items-center text-fg-muted">
                            <x-ui.icon name="search" class="h-5 w-5" />
                        </span>
                        <input type="search" name="search" id="searchInput" value="{{ request('search') }}"
                            placeholder="Ketik nama produk..."
                            class="h-12 w-full rounded-field border border-line-strong bg-surface pl-11 pr-11 text-sm text-fg placeholder:text-fg-muted"
                            autocomplete="off">

                        @if (request('search'))
                            <a href="{{ $activeStore ? route('store.products', [$activeStore, 'category' => request('category')]) : route('products', ['category' => request('category')]) }}"
                                class="absolute inset-y-0 right-1 flex w-10 items-center justify-center rounded-md text-fg-muted transition hover:text-fg"
                                aria-label="Hapus kata kunci pencarian">
                                <x-ui.icon name="close" class="h-4 w-4" />
                            </a>
                        @endif
                    </div>
                </div>

                <div class="sm:w-56">
                    <label for="categorySelect" class="mb-1.5 block text-sm font-medium text-fg-secondary">Kategori</label>
                    <select name="category" id="categorySelect"
                        class="h-12 w-full rounded-field border border-line-strong bg-surface px-3 text-sm text-fg">
                        <option value="">Semua kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}"
                                {{ (string) request('category') === (string) $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <noscript>
                    <x-ui.button type="submit" variant="outline" class="sm:mb-0">Terapkan</x-ui.button>
                </noscript>
            </form>

            @if (request('search') || request('category'))
                <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-fg-muted">
                    <span>Filter aktif:</span>

                    @if (request('search'))
                        <x-ui.badge variant="info">Pencarian: {{ request('search') }}</x-ui.badge>
                    @endif

                    @if (request('category'))
                        @php $activeCategory = $categories->firstWhere('id', (int) request('category')); @endphp
                        @if ($activeCategory)
                            <x-ui.badge variant="info">Kategori: {{ $activeCategory->category_name }}</x-ui.badge>
                        @endif
                    @endif

                    <a href="{{ $productsUrl }}"
                        class="font-semibold text-info underline underline-offset-2 hover:text-fg">Reset filter</a>
                </div>
            @endif
        </section>

        <div class="mt-6 flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm text-fg-muted" aria-live="polite" id="productsCount">
                @if ($products->total() > 0)
                    Menampilkan <span class="font-semibold text-fg">{{ $products->total() }}</span> produk
                @else
                    Tidak ada produk yang cocok
                @endif
            </p>
        </div>

        {{-- Error state (fetch) --}}
        <div id="productsError" class="mt-4 hidden">
            <x-ui.alert variant="danger" title="Gagal memuat produk">
                Periksa koneksi Anda, lalu coba lagi.
                <button type="button" id="retryProducts" class="ml-1 font-semibold underline underline-offset-2">Coba
                    lagi</button>
            </x-ui.alert>
        </div>

        {{-- Loading skeleton --}}
        <div id="productsLoading" class="mt-6 hidden grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4"
            aria-hidden="true">
            @for ($i = 0; $i < 8; $i++)
                <div class="overflow-hidden rounded-card border border-line bg-surface">
                    <div class="skeleton aspect-[4/3] w-full rounded-none"></div>
                    <div class="space-y-3 p-4">
                        <div class="skeleton h-4 w-4/5"></div>
                        <div class="skeleton h-3 w-2/5"></div>
                        <div class="skeleton h-5 w-1/2"></div>
                        <div class="skeleton h-10 w-full"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Grid --}}
        <div id="productsGrid" class="mt-6 grid grid-cols-2 gap-4 sm:gap-5 md:grid-cols-3 lg:grid-cols-4">
            @forelse ($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full">
                    <x-ui.empty-state title="Tidak ada produk yang cocok"
                        description="Coba kata kunci lain atau reset filter untuk melihat semua produk." icon="search">
                        <x-ui.button :href="$productsUrl" variant="outline">Reset filter</x-ui.button>
                    </x-ui.empty-state>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div id="paginationLinks" class="mt-8">
            <x-ui.pagination :paginator="$products" />
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categorySelect');
            const productsGrid = document.getElementById('productsGrid');
            const paginationLinks = document.getElementById('paginationLinks');
            const filterForm = document.getElementById('filterForm');
            const loading = document.getElementById('productsLoading');
            const errorBox = document.getElementById('productsError');
            const retryButton = document.getElementById('retryProducts');
            const countLabel = document.getElementById('productsCount');

            if (!searchInput || !productsGrid) {
                return;
            }

            let debounceTimeout;
            let controller = null;

            function setLoading(isLoading) {
                loading?.classList.toggle('hidden', !isLoading);
                if (isLoading) {
                    loading?.classList.add('grid');
                    productsGrid.classList.add('hidden');
                } else {
                    productsGrid.classList.remove('hidden');
                }
            }

            function escapeHtml(value) {
                const div = document.createElement('div');
                div.textContent = value ?? '';
                return div.innerHTML;
            }

            function starsHtml(rating) {
                const rounded = Math.round(rating || 0);
                let output = '';

                for (let i = 1; i <= 5; i += 1) {
                    output +=
                        `<svg class="h-3.5 w-3.5 ${i <= rounded ? 'text-rating' : 'text-line-strong'}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.947a1 1 0 0 0 .95.69h4.148c.969 0 1.371 1.24.588 1.81l-3.36 2.44a1 1 0 0 0-.364 1.118l1.287 3.948c.3.921-.755 1.688-1.538 1.118l-3.36-2.44a1 1 0 0 0-1.175 0l-3.36 2.44c-.783.57-1.838-.197-1.538-1.118l1.286-3.947a1 1 0 0 0-.364-1.118L2.037 9.374c-.783-.57-.38-1.81.588-1.81h4.148a1 1 0 0 0 .95-.69l1.286-3.947Z"/></svg>`;
                }

                return output;
            }

            function productCardHtml(product) {
                const ratingCount = Number(product.ratingCount || 0);
                const image = product.image_url || '{{ asset('images/gambar3.jpeg') }}';
                const price = Number(product.selling_price || 0).toLocaleString('id-ID');

                return `
                    <article class="group flex h-full flex-col overflow-hidden rounded-card border border-line bg-surface shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                        <a href="/product/${product.id}" class="relative block aspect-[4/3] overflow-hidden bg-surface-soft" tabindex="-1" aria-hidden="true">
                            <img src="${escapeHtml(image)}" alt="" loading="lazy" class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.03]">
                        </a>
                        <div class="flex flex-1 flex-col gap-2 p-4">
                            <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-fg sm:text-base">
                                <a href="/product/${product.id}" class="rounded transition hover:text-cta">${escapeHtml(product.product_name)}</a>
                            </h3>
                            <div class="flex items-center gap-1.5" role="img" aria-label="Rating ${Number(product.avgRating || 0).toFixed(1)} dari 5 berdasarkan ${ratingCount} ulasan">
                                <div class="flex items-center gap-0.5" aria-hidden="true">${starsHtml(product.avgRating)}</div>
                                ${ratingCount > 0 ? `<span class="text-xs text-fg-muted">(${ratingCount})</span>` : ''}
                            </div>
                            <p class="mt-auto pt-1 text-lg font-bold tracking-tight text-fg">Rp ${price}</p>
                            <form action="{{ route('addToCart') }}" method="POST">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" name="product_id" value="${product.id}">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-3 text-sm font-semibold text-fg transition hover:border-cta hover:bg-cta hover:text-on-cta">
                                    Tambah ke Keranjang
                                </button>
                            </form>
                        </div>
                    </article>
                `;
            }

            function renderProducts(products) {
                errorBox?.classList.add('hidden');

                if (!products.length) {
                    productsGrid.innerHTML = `
                        <div class="col-span-full">
                            <div class="flex flex-col items-center justify-center rounded-panel border border-dashed border-line-strong bg-surface px-6 py-12 text-center">
                                <h2 class="text-lg font-semibold text-fg">Tidak ada produk yang cocok</h2>
                                <p class="mt-1 max-w-md text-sm text-fg-muted">Coba kata kunci lain atau reset filter untuk melihat semua produk.</p>
                                <a href="{{ $productsUrl }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-field border border-line-strong bg-surface px-4 text-sm font-semibold text-fg hover:bg-surface-soft">Reset filter</a>
                            </div>
                        </div>
                    `;
                    return;
                }

                productsGrid.innerHTML = products.map(productCardHtml).join('');
            }

            function updateCount(total) {
                if (!countLabel) {
                    return;
                }

                countLabel.innerHTML = total > 0 ?
                    `Menampilkan <span class="font-semibold text-fg">${total}</span> produk` :
                    'Tidak ada produk yang cocok';
            }

            async function fetchProducts() {
                controller?.abort();
                controller = new AbortController();
                const activeController = controller;

                const searchQuery = searchInput.value.trim();
                const category = categorySelect.value;
                const url = new URL("{{ route('products.search') }}", window.location.origin);

                @if ($activeStore)
                    url.searchParams.append('store', "{{ $activeStore->id }}");
                @endif

                if (searchQuery) {
                    url.searchParams.append('q', searchQuery);
                }

                if (category) {
                    url.searchParams.append('category', category);
                }

                setLoading(true);
                errorBox?.classList.add('hidden');

                try {
                    const response = await fetch(url.toString(), {
                        signal: controller.signal,
                        headers: {
                            Accept: 'application/json'
                        },
                    });

                    if (!response.ok) {
                        throw new Error('Gagal memuat produk');
                    }

                    const products = await response.json();
                    renderProducts(products);
                    updateCount(products.length);
                    paginationLinks.innerHTML = '';
                } catch (error) {
                    if (error.name !== 'AbortError') {
                        errorBox?.classList.remove('hidden');
                    }
                } finally {
                    if (controller === activeController) {
                        setLoading(false);
                    }
                }
            }

            function debounce(callback, delay) {
                return function(...args) {
                    clearTimeout(debounceTimeout);
                    debounceTimeout = setTimeout(() => callback.apply(this, args), delay);
                };
            }

            const debouncedFetch = debounce(fetchProducts, 300);

            searchInput.addEventListener('input', debouncedFetch);

            categorySelect.addEventListener('change', function() {
                filterForm.submit();
            });

            retryButton?.addEventListener('click', fetchProducts);
        });
    </script>
@endpush
