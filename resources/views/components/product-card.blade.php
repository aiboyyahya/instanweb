@props(['product'])

@php
    // Catatan: harga yang dipakai keranjang & checkout saat ini berasal dari
    // purchase_price (lihat analisisweb.md H1). Tampilan disamakan dengan
    // nilai transaksi agar tidak menampilkan harga yang berbeda saat checkout.
    $price = $product->purchase_price ?? 0;
    $stock = (int) ($product->stock ?? 0);
    $image = $product->image ? asset('storage/' . $product->image) : asset('images/gambar3.jpeg');
    $ratingCount = (int) ($product->ratingCount ?? 0);
    $avgRating = $product->avgRating ?? null;
@endphp

<article
    {{ $attributes->merge(['class' => 'group flex h-full flex-col overflow-hidden rounded-card border border-line bg-surface shadow-sm transition duration-200 hover:-translate-y-0.5 hover:shadow-md']) }}>
    <a href="{{ route('product.show', $product->id) }}" class="relative block aspect-[4/3] overflow-hidden bg-surface-soft"
        tabindex="-1" aria-hidden="true">
        <img src="{{ $image }}" alt="" loading="lazy"
            class="h-full w-full object-cover transition duration-200 group-hover:scale-[1.03]">

        @if ($stock <= 0)
            <span
                class="absolute left-3 top-3 rounded-full bg-danger-strong px-2.5 py-1 text-xs font-semibold text-white shadow-sm">Stok
                habis</span>
        @endif
    </a>

    <div class="flex flex-1 flex-col gap-2 p-4">
        <h3 class="line-clamp-2 text-sm font-semibold leading-snug text-fg sm:text-base">
            <a href="{{ route('product.show', $product->id) }}"
                class="rounded transition hover:text-cta focus-visible:text-cta">
                {{ $product->product_name }}
            </a>
        </h3>

        <div class="flex items-center justify-between gap-2">
            <x-ui.rating-stars :rating="$avgRating" :count="$ratingCount" />

            @if ($stock > 0 && $stock <= 5)
                <span class="text-xs font-medium text-warning">Sisa {{ $stock }}</span>
            @endif
        </div>

        @if (! empty($product->description))
            <p class="line-clamp-2 text-xs leading-relaxed text-fg-muted sm:text-sm">
                {{ \Illuminate\Support\Str::limit(strip_tags($product->description), 90) }}
            </p>
        @endif

        <p class="mt-auto pt-1 text-lg font-bold tracking-tight text-fg">
            Rp {{ number_format($price, 0, ',', '.') }}
        </p>

        <form action="{{ route('addToCart') }}" method="POST">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            <input type="hidden" name="quantity" value="1">

            <button type="submit" @disabled($stock <= 0)
                class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-field border border-line-strong bg-surface px-3 text-sm font-semibold text-fg transition hover:border-cta hover:bg-cta hover:text-on-cta disabled:pointer-events-none disabled:opacity-50"
                aria-label="Tambah {{ $product->product_name }} ke keranjang">
                <x-ui.icon name="shopping-bag" class="h-4 w-4" />
                {{ $stock > 0 ? 'Tambah ke Keranjang' : 'Stok habis' }}
            </button>
        </form>
    </div>
</article>
