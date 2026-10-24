<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Rating;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    protected $midtransService;
    protected $apiUrl;
    protected $token;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
        $this->apiUrl = env('WA_API_URL', 'https://api.fonnte.com/send');
        $this->token = env('WA_API_TOKEN');
    }

    public function searchProducts(Request $request)
    {
        $query = Product::query();

        if ($request->filled('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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
                'purchase_price' => $product->purchase_price,
                'image_url' => asset('storage/' . $product->image),
                'avgRating' => $ratings->has($product->id) ? (float) $ratings[$product->id]->avg_rating : null,
                'ratingCount' => $ratings->has($product->id) ? (int) $ratings[$product->id]->rating_count : 0,
            ];
        });

        return response()->json($result);
    }

    public function index()
    {
        $products = Product::latest()->take(8)->get();

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

        return view('home', compact('products'));
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

        $products = $query->paginate(12);
        $categories = Category::all();

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

        return view('products', compact('products', 'categories'));
    }

    public function Product($id)
    {
        $product = Product::findOrFail($id);
        $avgRating = (float) Rating::where('product_id', $product->id)->avg('rating');
        $ratingCount = (int) Rating::where('product_id', $product->id)->count();
        $ratings = Rating::where('product_id', $product->id)->with('customer')->latest()->get();

        return view('detail', compact('product', 'avgRating', 'ratingCount', 'ratings'));
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
        $cart = session()->get('cart', []);

        if (isset($cart[$request->product_id])) {
            $cart[$request->product_id]['quantity'] += $request->quantity;
        } else {
            $cart[$request->product_id] = [
                'name' => $product->product_name,
                'price' => $product->purchase_price,
                'quantity' => $request->quantity,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);

        return redirect()->route('cart')->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function viewCart()
    {
        $cart = session()->get('cart', []);
        return view('cart', compact('cart'));
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

    public function checkoutPage(Request $request)
    {
        $cart = session()->get('cart', []);
        $directCheckout = null;

        if ($request->has('product_id') && $request->has('quantity') && $request->has('direct')) {
            $product = Product::findOrFail($request->product_id);
            $directCheckout = [
                $request->product_id => [
                    'name' => $product->product_name,
                    'price' => $product->purchase_price,
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

        return view('Checkout', compact('cart', 'directCheckout', 'totalWeight', 'originCityId', 'originDistrictId', 'defaultItemWeight'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string',
            'notes' => 'nullable|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'district' => 'required|string',
            'postal_code' => 'required|string',
            'courier' => 'required|string',
            'courier_service' => 'required|string',
            'shipping_cost' => 'required|numeric|min:0',
        ]);

        $phone = $request->phone_number;

        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        if (!preg_match('/^(62)/', $phone)) {
            return back()->with('error', 'Format nomor WA salah. Gunakan 08xxxx atau 62xxxx.');
        }

        $request->merge(['phone_number' => $phone]);

        $cart = session()->get('cart', []);
        $directCheckout = session()->get('direct_checkout', []);

        $itemsToProcess = !empty($directCheckout) ? $directCheckout : $cart;

        if (empty($itemsToProcess)) {
            return redirect()->route('checkout.page')->with('error', 'Keranjang kosong!');
        }

        $total = 0;
        foreach ($itemsToProcess as $id => $item) {
            $product = Product::find($id);
            if (!$product) {
                return back()->with('error', "Produk {$item['name']} tidak ditemukan.");
            }

            if ($product->stock < $item['quantity']) {
                return back()->with('error', "Stok {$item['name']} tidak cukup.");
            }

            $total += $item['price'] * $item['quantity'];
        }

        $total += $request->shipping_cost;

        $orderCode = 'ORD-' . time() . '-' . Auth::id();

        $transaction = Transaction::create([
            'order_code' => $orderCode,
            'customer_id' => Auth::id(),
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'status' => 'pending',
            'total' => $total,
            'shipping_cost' => $request->shipping_cost,
            'notes' => $request->notes,
            'payment_method' => 'midtrans',
            'payment_status' => 'pending',
            'province' => $request->province,
            'city' => $request->city,
            'district' => $request->district,
            'postal_code' => $request->postal_code,
            'courier' => $request->courier,
            'courier_service' => $request->courier_service,
        ]);

        foreach ($itemsToProcess as $id => $item) {
            $product = Product::find($id);
            $product->decrement('stock', $item['quantity']);

            TransactionItem::create([
                'transaction_id' => $transaction->id,
                'product_id' => $id,
                'price' => $item['price'],
                'quantity' => $item['quantity'],
            ]);
        }

        try {
            $customer = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => $phone,
            ];

            $result = $this->midtransService->createTransaction($transaction, $customer);
            $snapToken = $result['token'] ?? null;

            if (!$snapToken) {
                throw new \Exception('Snap token not found.');
            }

            $transaction->update(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans error: ' . $e->getMessage());
        }

        try {
            $this->sendOrderConfirmation($transaction);
        } catch (\Exception $e) {
            Log::error('Error kirim WA Fonnte: ' . $e->getMessage());
        }

        if (!empty($directCheckout)) {
            session()->forget('direct_checkout');
        } else {
            session()->forget('cart');
        }

        return redirect()->route('checkout.payment', $transaction->id)
            ->with('success', 'Checkout berhasil! Silakan lakukan pembayaran.');
    }

    public function checkoutPayment($id)
    {
        $transaction = Transaction::with('items.product')->findOrFail($id);
        return view('Checkout.Payment', compact('transaction'));
    }

    public function checkoutSuccess($id)
    {
        $transaction = Transaction::with('items.product')->findOrFail($id);
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
        $transaction = Transaction::where('customer_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        foreach ($transaction->items as $item) {
            $product = $item->product;
            $product->increment('stock', $item->quantity);
        }

        $transaction->delete();

        return redirect()->route('orders')->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function getSnapToken($id)
    {
        $transaction = Transaction::with('items.product')->findOrFail($id);

        if ($transaction->customer_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($transaction->snap_token) {
            return response()->json(['snap_token' => $transaction->snap_token]);
        }

        try {
            $customer = [
                'name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->phone ?? '',
            ];

            $result = $this->midtransService->createTransaction($transaction, $customer);
            $snapToken = $result['token'] ?? null;

            if (!$snapToken) {
                throw new \Exception('Snap token not found in Midtrans response');
            }

            $transaction->update(['snap_token' => $snapToken]);

            return response()->json(['snap_token' => $snapToken]);
        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token creation failed: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create snap token'], 500);
        }
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
        $store = Store::first();
        $storeName = $store ? $store->store_name : 'Toko Kami';

        $customer = $transaction->customer;
        $phone_number = $transaction->phone_number ?? $customer->phone_number ?? $customer->phone;

        if (!$phone_number) {
            Log::warning('No phone number found for transaction', ['transaction_id' => $transaction->id, 'customer_id' => $customer->id]);
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
            "{$transaction->address}\n" .
            "{$transaction->district}, {$transaction->city}\n" .
            "{$transaction->province} {$transaction->postal_code}\n\n" .
            "*Kurir:* {$transaction->courier} - {$transaction->courier_service}\n" .
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
