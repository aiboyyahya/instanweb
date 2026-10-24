<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    public function create(Request $request)
    {
        // Expect product_id and transaction_id as query parameters
        $productId = $request->query('product_id');
        $transactionId = $request->query('transaction_id');

        $product = null;
        if ($productId) {
            $product = Product::find($productId);
        }

        return view('review.create', [
            'product' => $product,
            'transaction_id' => $transactionId,
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'transaction_id' => 'required|exists:transactions,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048', // 2MB max
        ]);

        // Cek apakah user sudah memberikan rating untuk produk ini di transaksi ini
        $existingRating = Rating::where('customer_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->where('transaction_id', $request->transaction_id)
            ->first();

        if ($existingRating) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // Cek apakah transaksi milik user dan statusnya done atau success
        $transaction = Transaction::where('id', $request->transaction_id)
            ->where('customer_id', Auth::id())
            ->whereIn('status', ['success', 'done'])
            ->first();

        if (!$transaction) {
            return back()->with('error', 'Transaksi tidak valid.');
        }

        // Cek apakah produk ada di transaksi ini
        $transactionItem = $transaction->items()->where('product_id', $request->product_id)->first();
        if (!$transactionItem) {
            return back()->with('error', 'Produk tidak ditemukan dalam transaksi ini.');
        }

        $ratingData = [
            'customer_id' => Auth::id(),
            'product_id' => $request->product_id,
            'transaction_id' => $request->transaction_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('ratings', 'public');
            $ratingData['image'] = $imagePath;
        }

        try {
            Rating::create($ratingData);
        } catch (\Exception $e) {
            Log::error('Rating creation error: ' . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan ulasan: ' . $e->getMessage());
        }

        return back()->with('success', 'Ulasan berhasil dikirim. Terima kasih!');
    }

    public function update(Request $request, Rating $rating)
    {
        // Pastikan user hanya bisa edit ulasan miliknya sendiri
        if ($rating->customer_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk mengedit ulasan ini.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ]);

        $updateData = [
            'rating' => $request->rating,
            'comment' => $request->comment,
        ];

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($rating->image) {
                Storage::disk('public')->delete($rating->image);
            }

            $imagePath = $request->file('image')->store('ratings', 'public');
            $updateData['image'] = $imagePath;
        }

        $rating->update($updateData);

        return back()->with('success', 'Ulasan berhasil diperbarui.');
    }

    public function destroy(Rating $rating)
    {
        // Pastikan user hanya bisa hapus ulasan miliknya sendiri
        if ($rating->customer_id !== Auth::id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus ulasan ini.');
        }

        // Hapus gambar jika ada
        if ($rating->image) {
            Storage::disk('public')->delete($rating->image);
        }

        $rating->delete();

        return back()->with('success', 'Ulasan berhasil dihapus.');
    }
}
