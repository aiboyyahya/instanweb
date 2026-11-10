<?php

namespace App\Http\Controllers;

use App\Exceptions\CheckoutException;
use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\RegionService;
use App\Services\ShippingCostService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HomeController extends Controller
{



    public function searchProducts(Request $request)
    {
        $query = Product::query();

        if ($request->filled('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('store')) {
            $query->where('store_id', $request->store);
        }

        $products = $query->with('category')->limit(12)->get();

        $productIds = $products->pluck('id');
        $ratings = Rating::whereIn('product_id', $productIds)
            ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as rating_count')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $result = $products->map(function ($product) use ($ratings) {
            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'category_name' => $product->category->category_name ?? '',
                'selling_price' => $product->selling_price,
                'image_url' => asset('storage/' . $product->image),
                'avgRating' => $ratings->has($product->id) ? (float) $ratings[$product->id]->avg_rating : null,
                'ratingCount' => $ratings->has($product->id) ? (int) $ratings[$product->id]->rating_count : 0,
            ];
        });

        return response()->json($result);
    }

    public function index(Request $request)
    {
        // Beranda pembeli menampilkan direktori toko UMKM. Setiap kartu
        // mengarah ke storefront mandiri milik pemilik toko.
        $search = trim((string) $request->input('q', ''));

        $stores = Store::query()
            ->withCount(['products' => fn ($query) => $query->where('status', 'active')])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('store_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%');
                });
            })
            ->orderByDesc('created_at')
            ->get();

        return view('home', compact('stores', 'search'));
    }

    public function storeShow(Store $store)
    {
        session(['active_store_id' => $store->id]);

        $products = $store->products()
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();

        $this->attachRatings($products);

        return view('store.show', compact('store', 'products'));
    }

    public function storeProducts(Request $request, Store $store)
    {
        session(['active_store_id' => $store->id]);

        $query = $store->products()->where('status', 'active');

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = $store->categories()
            ->orderBy('category_name')
            ->get();

        $this->attachRatings($products);

        return view('products', compact('products', 'categories', 'store'));
    }

    /**
     * Halaman kontak khusus satu toko.
     */
    public function storeContact(Store $store)
    {
        session(['active_store_id' => $store->id]);

        return view('kontak', compact('store'));
    }

    protected function attachRatings($products): void
    {
        $productIds = $products->pluck('id');

        $ratings = Rating::whereIn('product_id', $productIds)
            ->selectRaw('product_id, AVG(rating) as avg_rating, COUNT(*) as rating_count')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $product->avgRating = $ratings->has($product->id) ? (float) $ratings[$product->id]->avg_rating : null;
            $product->ratingCount = $ratings->has($product->id) ? (int) $ratings[$product->id]->rating_count : 0;
        }
    }

    public function kontak()
    {
        $store = Store::first();
        return view('kontak', compact('store'));
    }

    public function products(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('product_name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::all();

        $this->attachRatings($products);

        return view('products', compact('products', 'categories'));
    }

    public function Product($id)
    {
        $product = Product::with('store')->findOrFail($id);

        if ($product->store_id) {
            session(['active_store_id' => $product->store_id]);
        }

        $avgRating = (float) Rating::where('product_id', $product->id)->avg('rating');
        $ratingCount = (int) Rating::where('product_id', $product->id)->count();
        $ratings = Rating::where('product_id', $product->id)->with('customer')->latest()->get();

        return view('Detail', compact('product', 'avgRating', 'ratingCount', 'ratings'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($request->action === 'checkout') {
            return redirect()->route('checkout.page', ['product_id' => $request->product_id, 'quantity' => $request->quantity]);
        }

        $product = Product::findOrFail($request->product_id);

        if ($product->store_id) {
            session(['active_store_id' => $product->store_id]);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] += $request->quantity;
            $cart[$request->product_id]['store_id'] = $product->store_id;
        } else {
            $cart[$request->product_id] = [
                'name' => $product->product_name,
                'price' => $product->selling_price,
                'quantity' => $request->quantity,
                'image' => $product->image,
                'store_id' => $product->store_id,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function viewCart()
    {
        $cart = cart_items();

        return view('Cart', compact('cart'));
    }

    public function removeCart($id)
    {
        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return redirect()->route('cart')->with('success', 'Barang berhasil dihapus dari keranjang!');
    }

    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = $request->quantity;
            session()->put('cart', $cart);
        }

        return redirect()->route('cart')->with('success', 'Jumlah produk berhasil diperbarui!');
    }

    public function checkoutPage(Request $request, RegionService $regions)
    {
        $cart = cart_items();
        $directCheckout = null;

        if ($request->has('product_id') && $request->has('quantity') && $request->has('direct')) {
            $product = Product::findOrFail($request->product_id);

            if ($product->store_id) {
                session(['active_store_id' => $product->store_id]);
            }

            $directCheckout = [
                $request->product_id => [
                    'name' => $product->product_name,
                    'price' => $product->selling_price,
                    'quantity' => (int) $request->quantity,
                    'image' => $product->image,
                    'weight' => $product->weight ?? 1000,
                ]
            ];
            session()->put('direct_checkout', $directCheckout);
        }

        if (empty($cart) && empty($directCheckout)) {
            return redirect()->route('cart')->with('error', 'Keranjang kosong!');
        }

        $totalWeight = 0;
        $items = !empty($directCheckout) ? $directCheckout : $cart;
        foreach ($items as $item) {
            $totalWeight += ($item['weight'] ?? 1000) * $item['quantity'];
        }

        $originCityId = config('services.rajaongkir.origin_city_id', 469);
        $originDistrictId = config('services.rajaongkir.origin_district_id');
        $defaultItemWeight = 1000;

        $originStore = store_context() ?? store();
        $originLatitude = $originStore?->latitude;
        $originLongitude = $originStore?->longitude;
        $originStoreName = $originStore?->store_name;

        $selectedProvince = old('province_id') ? $regions->findProvince((string) old('province_id')) : null;
        $selectedCity = old('city_id') ? $regions->findRegency((string) old('city_id')) : null;
        $selectedDistrict = old('district_id') ? $regions->findDistrict((string) old('district_id')) : null;

        $selectedRegions = [
            'province' => $selectedProvince['name'] ?? null,
            'city' => $selectedCity['name'] ?? null,
            'district' => $selectedDistrict['name'] ?? null,
        ];

        return view('Checkout', compact(
            'cart',
            'directCheckout',
            'totalWeight',
            'originCityId',
            'originDistrictId',
            'defaultItemWeight',
            'selectedRegions',
            'originLatitude',
            'originLongitude',
            'originStoreName',
        ));
    }

    /**
     * Estimasi ongkir berdasarkan titik lokasi toko dan titik pengiriman.
     */
    public function shippingEstimate(Request $request, ShippingCostService $shipping)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'courier' => ['required', 'string', 'max:50', Rule::in($shipping->supportedCouriers())],
        ]);

        $originStore = $this->resolveCheckoutOriginStore();
        $courier = $shipping->canonicalCourier((string) $validated['courier']);

        $estimate = $shipping->estimate(
            $originStore?->latitude !== null ? (float) $originStore->latitude : null,
            $originStore?->longitude !== null ? (float) $originStore->longitude : null,
            (float) $validated['latitude'],
            (float) $validated['longitude'],
            $courier,
        );

        return response()->json([
            'distance_km' => $estimate['distance_km'],
            'shipping_cost' => $estimate['shipping_cost'],
            'courier' => $courier,
            'courier_label' => $shipping->courierLabel($courier),
            'origin_configured' => $originStore?->latitude !== null && $originStore?->longitude !== null,
        ]);
    }

    /**
     * Toko asal pengiriman mengikuti produk yang sedang di-checkout.
     */
    private function resolveCheckoutOriginStore(): ?Store
    {
        $directCheckout = session('direct_checkout', []);
        $items = ! empty($directCheckout) ? $directCheckout : cart_items();
        $firstProductId = array_key_first($items);

        if ($firstProductId !== null) {
            $productStore = Product::query()
                ->with('store')
                ->find((int) $firstProductId)?->store;

            if ($productStore) {
                return $productStore;
            }
        }

        return store_context() ?? store();
    }

    public function checkout(Request $request, RegionService $regions, ShippingCostService $shipping)
    {
        $request->validate([
            'province_id' => 'required|string|max:20',
            'city_id' => 'required|string|max:20',
            'district_id' => 'required|string|max:20',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'postal_code' => 'nullable|string|max:5',
            'courier' => ['required', 'string', 'max:50', Rule::in($shipping->supportedCouriers())],
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'payment_method' => 'required|in:transfer_bank,cod,qris,manual',
        ]);

        $province = $regions->findProvince((string) $request->province_id);
        $regency = $regions->findRegency((string) $request->city_id);
        $district = $regions->findDistrict((string) $request->district_id);

        if (
            ! $province
            || ! $regency
            || ! $district
            || $regency['province_id'] !== $province['id']
            || $district['regency_id'] !== $regency['id']
        ) {
            throw ValidationException::withMessages([
                'province_id' => 'Pilih provinsi, kota/kabupaten, dan kecamatan yang sesuai.',
            ]);
        }

        $phone = $request->phone_number;

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (!preg_match('/^(62)/', $phone)) {
            return back()->with('error', 'Format nomor WA salah. Gunakan 08xxxx atau 62xxxx.');
        }

        $request->merge(['phone_number' => $phone]);

        $cart = cart_items();
        $directCheckout = session()->get('direct_checkout', []);

        $itemsToProcess = !empty($directCheckout) ? $directCheckout : $cart;

        if (empty($itemsToProcess)) {
            return redirect()->route('checkout.page')->with('error', 'Keranjang kosong!');
        }

        try {
            $transaction = DB::transaction(function () use ($itemsToProcess, $request, $province, $regency, $district, $shipping) {
                $productIds = array_map('intval', array_keys($itemsToProcess));

                // Kunci baris produk agar stok tidak berubah di tengah proses checkout.
                $products = Product::query()
                    ->whereIn('id', $productIds)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('id');

                $total = 0;
                $preparedItems = [];

                foreach ($itemsToProcess as $id => $item) {
                    $product = $products->get((int) $id);
                    $quantity = (int) ($item['quantity'] ?? 0);

                    if (! $product) {
                        throw new CheckoutException('Produk pada keranjang tidak ditemukan. Silakan perbarui keranjang.');
                    }

                    if ($quantity < 1) {
                        throw new CheckoutException('Jumlah produk pada keranjang tidak valid.');
                    }

                    if ($product->stock < $quantity) {
                        throw new CheckoutException("Stok {$product->product_name} tidak cukup.");
                    }

                    // Harga selalu diambil dari database, bukan dari session/keranjang.
                    $unitPrice = (float) $product->selling_price;
                    $total += $unitPrice * $quantity;

                    $preparedItems[] = [
                        'product' => $product,
                        'price' => $unitPrice,
                        'quantity' => $quantity,
                    ];
                }

                $originStore = ! empty($preparedItems) ? ($preparedItems[0]['product']->store ?? null) : null;
                $courier = $shipping->canonicalCourier((string) $request->courier);

                $estimate = $shipping->estimate(
                    $originStore?->latitude !== null ? (float) $originStore->latitude : null,
                    $originStore?->longitude !== null ? (float) $originStore->longitude : null,
                    (float) $request->latitude,
                    (float) $request->longitude,
                    $courier,
                );

                $shippingCost = (int) $estimate['shipping_cost'];
                $distanceKm = $estimate['distance_km'];
                $total += $shippingCost;

                $orderCode = 'ORD-' . now()->format('YmdHis') . '-' . Auth::id() . '-' . Str::upper(Str::random(4));

                $transaction = Transaction::create([
                    'order_code' => $orderCode,
                    'customer_id' => Auth::id(),
                    'phone_number' => $request->phone_number,
                    'address' => $request->address,
                    'status' => 'pending',
                    'total' => $total,
                    'shipping_cost' => $shippingCost,
                    'distance_km' => $distanceKm,
                    'notes' => $request->notes,
                    'payment_method' => $request->payment_method,
                    'payment_status' => 'pending',
                    'province' => $province['name'],
                    'city' => $regency['name'],
                    'district' => $district['name'],
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'postal_code' => $request->postal_code,
                    'courier' => $courier,
                    'courier_service' => $shipping->courierService($courier),
                ]);

                foreach ($preparedItems as $prepared) {
                    $prepared['product']->decrement('stock', $prepared['quantity']);

                    TransactionItem::create([
                        'transaction_id' => $transaction->id,
                        'product_id' => $prepared['product']->id,
                        'price' => $prepared['price'],
                        'quantity' => $prepared['quantity'],
                    ]);
                }

                return $transaction;
            });
        } catch (CheckoutException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        // Kirim konfirmasi via WhatsApp (Fonnte)
        try {
            $this->sendOrderConfirmation($transaction);
        } catch (\Exception $e) {
            Log::error('Error kirim WA Fonnte: ' . $e->getMessage());
        }

        if (!empty($directCheckout)) {
            session()->forget('direct_checkout');
        } else {
            $remainingCart = session()->get('cart', []);

            foreach (array_keys($itemsToProcess) as $productId) {
                unset($remainingCart[$productId]);
            }

            session()->put('cart', $remainingCart);
        }

        // Langsung arahkan ke halaman sukses atau halaman instruksi pembayaran manual
        return redirect()->route('checkout.success', $transaction->id)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan konfirmasi pembayaran.');
    }

    public function checkoutPayment($id)
    {
        $transaction = Transaction::with('items.product')
            ->where('customer_id', Auth::id())
            ->findOrFail($id);

        return view('Checkout.Payment', compact('transaction'));
    }

    public function checkoutSuccess($id)
    {
        $transaction = Transaction::with('items.product')
            ->where('customer_id', Auth::id())
            ->findOrFail($id);
        $userRatings = collect();

        if (Auth::check()) {
            $userRatings = Rating::where('customer_id', Auth::id())
                ->where('transaction_id', $transaction->id)
                ->get()
                ->keyBy('product_id');
        }

        return view('Checkout.Success', compact('transaction', 'userRatings'));
    }

    public function orders()
    {
        $transactions = Transaction::with('items.product')
            ->where('customer_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pesanan', compact('transactions'));
    }

    public function orderDetail($id)
    {
        $transaction = Transaction::with('items.product')
            ->where('customer_id', Auth::id())
            ->findOrFail($id);

        return view('detail-pesanan', compact('transaction'));
    }

    public function deleteOrder($id)
    {
        $transaction = Transaction::with('items.product')
            ->where('customer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->items as $item) {
                $item->product?->increment('stock', $item->quantity);
            }

            $transaction->delete();
        });

        return redirect()->route('orders')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function sendMessage($phone_number, $message)
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.fonnte.com/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => array(
                    'target' => $phone_number,
                    'message' => $message,
                    'countryCode' => '62',
                ),
                CURLOPT_HTTPHEADER => array(
                    'Authorization: ' . env('WA_API_TOKEN'),
                ),
            ));

            $response = curl_exec($curl);
            $error = curl_error($curl);

            curl_close($curl);

            if ($error) {
                Log::error('Fonnte CURL ERROR', ['error' => $error]);
                return false;
            }

            $data = json_decode($response, true);

            Log::info('Fonnte API Response', ['response' => $data]);

            if (isset($data['status']) && $data['status'] == 'success') {
                Log::info('Fonnte message sent', [
                    'phone' => $phone_number,
                    'message' => $message,
                ]);
                return true;
            }

            Log::error('Fonnte failed to send message', [
                'phone' => $phone_number,
                'message' => $message,
                'response' => $response,
            ]);

            return false;
        } catch (\Exception $e) {
            Log::error('Fonnte Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function sendOrderConfirmation($transaction)
    {
        $transaction->loadMissing(['items.product.store', 'customer']);

        // Gunakan toko pemilik produk pada transaksi, bukan toko pertama.
        $store = $transaction->items
            ->map(fn ($item) => $item->product?->store)
            ->filter()
            ->first();

        $store ??= $transaction->customer?->store;
        $storeName = $store?->store_name ?: config('app.name', 'Toko Kami');

        $customer = $transaction->customer;
        $phone_number = $transaction->phone_number ?? $customer?->phone_number ?? $customer?->phone;

        if (! $phone_number) {
            Log::warning('No phone number found for transaction', [
                'transaction_id' => $transaction->id,
                'customer_id' => $transaction->customer_id,
            ]);

            return false;
        }

        $phone_number = $this->formatPhoneNumber($phone_number);

        $produkList = [];
        foreach ($transaction->items as $item) {
            $produkList[] = "- {$item->product->product_name} x{$item->quantity}";
        }
        $produkString = implode("\n", $produkList);

        $message = "*{$storeName}*\n\n" .
            "Pesanan Anda sudah kami proses!\n\n" .
            "*Detail Pesanan:*\n" .
            "Order Code: {$transaction->order_code}\n" .
            "Produk:\n{$produkString}\n\n" .
            "*Alamat Pengiriman:*\n" .
            ($transaction->address ? "{$transaction->address}\n" : '') .
            "{$transaction->district}, {$transaction->city}\n" .
            "{$transaction->province}" . ($transaction->postal_code ? " {$transaction->postal_code}" : '') . "\n\n" .
            "*Kurir:* {$transaction->courier} - {$transaction->courier_service}\n" .
            "*Pembayaran:* {$transaction->paymentMethodLabel()}\n" .
            "*Ongkir:* Rp" . number_format((float) ($transaction->shipping_cost ?? 0), 0, ',', '.') .
            ($transaction->distance_km ? " (jarak ± " . number_format((float) $transaction->distance_km, 2, ',', '.') . " km)" : '') . "\n" .
            "*Total:* Rp" . number_format($transaction->total, 0, ',', '.') . "\n\n" .
            "Terima kasih sudah berbelanja di Toko Kami {$storeName}!";

        return $this->sendMessage($phone_number, $message);
    }

    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        if (!str_starts_with($phone, '62')) {
            $phone = '62' . $phone;
        }

        return $phone;
    }
}
