<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonAnController;
use App\Http\Controllers\NhaHangController;
use App\Http\Controllers\GioHangController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderHistoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;

// Homepage Route
Route::get('/', [MonAnController::class, 'menu'])->name('foods.index');

// Food Detail Route
Route::get('/food/{id}', [MonAnController::class, 'detail'])->name('food.detail');

// Restaurant Detail Route
Route::get('/restaurant/{id}', [NhaHangController::class, 'show'])->name('restaurants.show');

// Cart Routes (work for both guest and authenticated users)
Route::get('/cart', [GioHangController::class, 'index'])->name('cart.index');
Route::post('/cart', [GioHangController::class, 'store'])->name('cart.store');
Route::put('/cart/{id}', [GioHangController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [GioHangController::class, 'destroy'])->name('cart.destroy');
Route::delete('/cart', [GioHangController::class, 'clear'])->name('cart.clear');

// Authentication Routes (must be before protected routes)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply-voucher');
    Route::post('/checkout/get-best-voucher', [CheckoutController::class, 'getBestVoucher'])->name('checkout.get-best-voucher');
    Route::get('/checkout/payment/{maDonHang}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/process-payment/{maDonHang}', [CheckoutController::class, 'processPayment'])->name('checkout.process-payment');
    Route::get('/checkout/success/{maDonHang}', [CheckoutController::class, 'success'])->name('checkout.success');

    // Order History Routes
    Route::get('/orders/history', [OrderHistoryController::class, 'index'])->name('orders.history');
    Route::get('/orders/{id}', [OrderHistoryController::class, 'show'])->name('orders.show');

    // Review Routes
    Route::get('/reviews/create/{donHang}/{monAn}', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/reviews/{donHang}/{monAn}', [ReviewController::class, 'store'])->name('reviews.store');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
});