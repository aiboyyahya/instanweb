<?php

use App\Http\Controllers\GoogleLoginController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\RajaOngkirController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\RegionController;

Route::get('/beranda', [HomeController::class, 'index'])->name('home');

// Storefront mandiri untuk setiap pemilik toko.
Route::get('/toko/{store:slug}', [HomeController::class, 'storeShow'])->name('store.show');
Route::get('/toko/{store:slug}/produk', [HomeController::class, 'storeProducts'])->name('store.products');
Route::get('/toko/{store:slug}/artikel', [ArticleController::class, 'storeIndex'])->name('store.articles.index');
Route::get('/toko/{store:slug}/artikel/{article:slug}', [ArticleController::class, 'storeShow'])->name('store.articles.show');
Route::get('/toko/{store:slug}/kontak', [HomeController::class, 'storeContact'])->name('store.contact');

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('home');
    }
    return view('selamatdatang');
})->name('selamatdatang');

// Alias publik untuk tautan lama/berbagi.
Route::view('/selamatdatang', 'selamatdatang')->name('welcome');

Route::get('/google/redirect', [GoogleLoginController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/login', [GoogleLoginController::class, 'loginForm'])->name('login.form');
Route::post('/login/email', [GoogleLoginController::class, 'loginEmail'])->name('login.email');
Route::get('/register', [GoogleLoginController::class, 'registerForm'])->name('register.form');
Route::post('/register/email', [GoogleLoginController::class, 'registerEmail'])->name('register.email');
Route::get('/register/toko', [GoogleLoginController::class, 'storeRegisterForm'])->name('register.store.form');
Route::post('/register/toko', [GoogleLoginController::class, 'storeRegister'])->name('register.store');
Route::get('/google/callback', [GoogleLoginController::class, 'handleGoogleCallback'])->name('google.callback');

Route::post('/logout', [GoogleLoginController::class, 'logout'])->name('logout');


Route::get('/produk', [HomeController::class, 'products'])->name('products');

Route::get('/product/{id}', [HomeController::class, 'Product'])->name('product.show');

Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

Route::get('/api/products/search', [HomeController::class, 'searchProducts'])->name('products.search');

// Data wilayah untuk pilihan provinsi, kabupaten/kota, dan kecamatan.
Route::get('/api/wilayah/provinsi', [RegionController::class, 'provinces'])->name('regions.provinces');
Route::get('/api/wilayah/kota', [RegionController::class, 'regencies'])->name('regions.regencies');
Route::get('/api/wilayah/kecamatan', [RegionController::class, 'districts'])->name('regions.districts');
Route::post('/api/wilayah/geocode', [RegionController::class, 'geocode'])->name('regions.geocode');

Route::get('/artikel', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/artikel/{article}', [ArticleController::class, 'show'])->name('articles.show');



Route::middleware(['auth'])->group(function () {
    // Verifikasi email dan nomor telepon.
    Route::get('/verifikasi', [\App\Http\Controllers\VerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/verifikasi/email/kirim', [\App\Http\Controllers\VerificationController::class, 'sendEmail'])->middleware('throttle:5,1')->name('verification.email.send');
    Route::post('/verifikasi/email/konfirmasi', [\App\Http\Controllers\VerificationController::class, 'confirmEmail'])->middleware('throttle:10,1')->name('verification.email.confirm');
    Route::post('/verifikasi/telepon', [\App\Http\Controllers\VerificationController::class, 'updatePhone'])->name('verification.phone.update');
    Route::post('/verifikasi/telepon/kirim', [\App\Http\Controllers\VerificationController::class, 'sendPhone'])->middleware('throttle:5,1')->name('verification.phone.send');
    Route::post('/verifikasi/telepon/konfirmasi', [\App\Http\Controllers\VerificationController::class, 'confirmPhone'])->middleware('throttle:10,1')->name('verification.phone.confirm');

    Route::post('/add-to-cart', [HomeController::class, 'addToCart'])->name('addToCart');
    Route::get('/cart', [HomeController::class, 'viewCart'])->name('cart');
    Route::patch('/cart/{id}', [HomeController::class, 'updateCart'])->name('updateCart');
    Route::delete('/cart/{id}', [HomeController::class, 'removeCart'])->name('removeCart');

    Route::middleware(['contact.verified'])->group(function () {
        Route::get('/checkout', [HomeController::class, 'checkoutPage'])->name('checkout.page');
        Route::post('/checkout', [HomeController::class, 'checkout'])->name('checkout');
        Route::post('/checkout/ongkir', [HomeController::class, 'shippingEstimate'])->name('checkout.shipping-estimate');
        Route::get('/checkout/payment/{id}', [HomeController::class, 'checkoutPayment'])->name('checkout.payment');
        Route::get('/checkout/success/{id}', [HomeController::class, 'checkoutSuccess'])->name('checkout.success');
    });
    Route::get('/pesanan', [HomeController::class, 'orders'])->name('orders');
    Route::get('/pesanan/{id}', [HomeController::class, 'orderDetail'])->name('order.detail');
    Route::delete('/pesanan/{id}', [HomeController::class, 'deleteOrder'])->name('order.delete');
    Route::get('/profile', [ProfilController::class, 'index'])->name('profil');
    Route::put('/profil', [ProfilController::class, 'update'])->name('profil.update');
    Route::put('/profil/toko', [ProfilController::class, 'updateStore'])->name('profil.store.update');
    Route::post('/profil/verify-password', [ProfilController::class, 'verifyPassword'])->name('profil.verify.password');
    // Ratings
    Route::post('/ratings', [\App\Http\Controllers\ReviewController::class, 'store'])->name('ratings.store');
    Route::get('/ratings/create', [\App\Http\Controllers\ReviewController::class, 'create'])->name('ratings.create');
    Route::put('/ratings/{rating}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('ratings.update');
    Route::delete('/ratings/{rating}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('ratings.destroy');
});
