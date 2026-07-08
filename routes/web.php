<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Auth\AuthController;
use App\Http\Controllers\Web\Client\MonAnController;
use App\Http\Controllers\Web\Client\NhaHangController;
use App\Http\Controllers\Web\Client\GioHangController;
use App\Http\Controllers\Web\Client\CheckoutController;
use App\Http\Controllers\Web\Client\OrderHistoryController;
use App\Http\Controllers\Web\Client\ProfileController;
use App\Http\Controllers\Web\Client\ReviewController;
use App\Http\Controllers\Web\Seller\SellerDashboardController;

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

// AI Chat Route
Route::post('/api/v1/ai-chat', [App\Http\Controllers\Web\Client\ChatController::class, 'sendMessage'])->name('ai-chat.send');
Route::post('/api/v1/ai-config', [App\Http\Controllers\Web\Client\ChatController::class, 'saveApiKey'])->name('ai-chat.config');

// Authentication Routes (must be before protected routes)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google Authentication Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/mock', [AuthController::class, 'showMockGoogleLogin'])->name('auth.google.mock');
Route::post('/auth/google/mock/callback', [AuthController::class, 'handleMockGoogleCallback'])->name('auth.google.mock.callback');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Checkout Routes
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher'])->name('checkout.apply-voucher');
    Route::post('/checkout/remove-voucher', [CheckoutController::class, 'removeVoucher'])->name('checkout.remove-voucher');
    Route::post('/checkout/calculate-shipping', [CheckoutController::class, 'calculateShipping'])->name('checkout.calculate-shipping');
    Route::post('/checkout/get-best-voucher', [CheckoutController::class, 'getBestVoucher'])->name('checkout.get-best-voucher');
    Route::post('/checkout/pre-apply-voucher', [CheckoutController::class, 'preApplyVoucher'])->name('checkout.pre-apply-voucher');
    Route::get('/checkout/payment/{maDonHang}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::post('/checkout/process-payment/{maDonHang}', [CheckoutController::class, 'processPayment'])->name('checkout.process-payment');
    Route::get('/checkout/payment-confirmation/{maDonHang}', [CheckoutController::class, 'paymentConfirmation'])->name('checkout.payment-confirmation');
    Route::get('/checkout/success/{maDonHang}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::post('/checkout/confirm-transfer/{maDonHang}', [CheckoutController::class, 'confirmTransfer'])->name('checkout.confirm-transfer');
    Route::get('/checkout/check-status/{maDonHang}', [CheckoutController::class, 'checkPaymentStatus'])->name('checkout.check-status');
    Route::get('/checkout/payos-return', [CheckoutController::class, 'payosReturn'])->name('checkout.payos-return');
    Route::get('/checkout/payos-cancel', [CheckoutController::class, 'payosCancel'])->name('checkout.payos-cancel');
    Route::get('/checkout/payos-resume/{maDonHang}', [CheckoutController::class, 'resumePayOSPayment'])->name('checkout.payos-resume');

    // Order History Routes - Using RESTful API
    Route::get('/orders/history', function() {
        return view('client.orders.history');
    })->name('orders.history');
    Route::get('/orders/{id}', function($id) {
        return view('client.orders.show', ['orderId' => $id]);
    })->name('orders.show');
    Route::post('/orders/{id}/cancel', [\App\Http\Controllers\Api\User\OrderController::class, 'cancel'])->name('orders.cancel');

    // Review Routes - Using RESTful API
    Route::get('/reviews/create/{donHang}/{monAn}', function($donHang, $monAn) {
        return view('client.reviews.create', ['orderId' => $donHang, 'foodId' => $monAn]);
    })->name('reviews.create');

    // Profile Routes - Using RESTful API
    Route::get('/profile', function() {
        return view('client.profile.index');
    })->name('profile.index');
    Route::get('/profile/edit', function() {
        return view('client.profile.edit');
    })->name('profile.edit');
    Route::post('/profile/update-address', [ProfileController::class, 'updateAddress'])->name('profile.update-address');
    Route::post('/profile/update-tag-address', [ProfileController::class, 'updateTagAddress'])->name('profile.update-tag-address');

    // Favorite Foods Routes
    Route::get('/favorites', [MonAnController::class, 'favorites'])->name('foods.favorites');
    Route::post('/favorites/{id}/toggle', [MonAnController::class, 'toggleFavorite'])->name('foods.toggle-favorite');

    // Seller Routes
    Route::prefix('seller')->name('seller.')->middleware('role:NguoiBan')->group(function () {
        Route::get('/dashboard', [SellerDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/foods', function() {
            return view('seller.foods.index');
        })->name('foods.index');
        Route::get('/foods/create', function() {
            $sellerId = auth()->user()->MaNguoiDung;
            $nhaHangs = \App\Models\NhaHang::where('MaNguoiDung', $sellerId)->get(['MaNhaHang', 'TenNhaHang']);
            $danhMuc = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();
            if (empty($danhMuc)) {
                $danhMuc = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'];
            }
            $trangThai = ['Còn bán', 'Ngừng bán'];
            return view('seller.foods.create', compact('nhaHangs', 'danhMuc', 'trangThai'));
        })->name('foods.create');
        Route::get('/foods/{id}/edit', function($id) {
            $food = \App\Models\MonAn::with('nhaHang')->findOrFail($id);
            if (!$food->nhaHang || $food->nhaHang->MaNguoiDung !== auth()->user()->MaNguoiDung) abort(403);
            $danhMuc = \App\Models\DanhMuc::where('TrangThai', 'Hoạt động')->pluck('TenDanhMuc')->toArray();
            if (empty($danhMuc)) {
                $danhMuc = ['Cơm', 'Bún', 'Phở', 'Mì', 'Trà'];
            }
            $trangThai = ['Còn bán', 'Ngừng bán'];
            return view('seller.foods.edit', compact('food', 'danhMuc', 'trangThai'));
        })->name('foods.edit');
        
        Route::get('/orders', function() {
            return view('seller.orders.index');
        })->name('orders.index');

        Route::get('/orders/{id}', function($id) {
            return view('seller.orders.show', compact('id'));
        })->name('orders.show');
        
        Route::get('/reports', function() {
            return view('seller.reports.index');
        })->name('reports.index');

        Route::get('/restaurant/edit', function() {
            $restaurant = \App\Models\NhaHang::where('MaNguoiDung', auth()->user()->MaNguoiDung)->first();
            if (!$restaurant) {
                $restaurant = new \App\Models\NhaHang();
                $restaurant->MaNguoiDung = auth()->user()->MaNguoiDung;
            }
            return view('seller.restaurant.edit', compact('restaurant'));
        })->name('restaurant.edit');

        // Reviews
        Route::prefix('reviews')->group(function () {
            Route::get('/', [\App\Http\Controllers\Web\Seller\ReviewController::class, 'index'])->name('reviews.index');
            Route::post('/{id}/reply', [\App\Http\Controllers\Web\Seller\ReviewController::class, 'reply'])->name('reviews.reply');
        });
    });

    // Seller root redirect
    Route::get('/seller', function() {
        return redirect()->route('seller.dashboard');
    })->name('seller.index');

    // Admin root redirect
    Route::get('/admin', function() {
        return redirect()->route('admin.dashboard');
    })->name('admin.index');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('role:QuanTri')->group(function () {
        Route::get('/dashboard', function() {
            return view('admin.dashboard.index');
        })->name('dashboard');

        Route::get('/reports', function() {
            return view('admin.reports.index');
        })->name('reports.index');

        Route::get('/vouchers', function() {
            return view('admin.vouchers.index');
        })->name('vouchers.index');
        
        Route::get('/vouchers/create', function() {
            return view('admin.vouchers.create');
        })->name('vouchers.create');
        
        Route::get('/vouchers/{id}/edit', function($id) {
            return view('admin.vouchers.edit', compact('id'));
        })->name('vouchers.edit');

        Route::get('/vouchers/{id}', function($id) {
            return view('admin.vouchers.show', compact('id'));
        })->name('vouchers.show');
        
        Route::get('/vouchers/export', function() {
            return "Tính năng export đang được phát triển.";
        })->name('vouchers.export');
        
        Route::get('/users', function() {
            return view('admin.users.index');
        })->name('users.index');
        
        Route::get('/users/create', function() {
            return view('admin.users.create');
        })->name('users.create');

        Route::get('/users/{id}', function($id) {
            return view('admin.users.show', ['id' => $id]);
        })->name('users.show');

        Route::get('/users/{id}/edit', function($id) {
            return view('admin.users.edit', ['id' => $id]);
        })->name('users.edit');
        
        Route::get('/doi-tac-van-chuyen', function() {
            return view('admin.carriers.index');
        })->name('doi-tac-van-chuyen.index');
        
        Route::get('/doi-tac-van-chuyen/create', function() {
            return view('admin.carriers.create');
        })->name('doi-tac-van-chuyen.create');
        
        Route::get('/doi-tac-van-chuyen/{id}/edit', function($id) {
            $doiTacVanChuyen = \App\Models\DoiTacVanChuyen::findOrFail($id);
            return view('admin.carriers.edit', compact('doiTacVanChuyen'));
        })->name('doi-tac-van-chuyen.edit');

        Route::get('/doi-tac-van-chuyen/{id}', function($id) {
            $doiTacVanChuyen = \App\Models\DoiTacVanChuyen::findOrFail($id);
            return view('admin.carriers.show', compact('doiTacVanChuyen'));
        })->name('doi-tac-van-chuyen.show');
        
        Route::post('/doi-tac-van-chuyen', function() {
        })->name('doi-tac-van-chuyen.store');

        Route::put('/doi-tac-van-chuyen/{id}', function() {
        })->name('doi-tac-van-chuyen.update');

        Route::get('/orders', function() {
            return view('admin.orders.index');
        })->name('orders.index');

        Route::get('/orders/{id}', function($id) {
            return view('admin.orders.show', compact('id'));
        })->name('orders.show');

        Route::get('/reconciliation', function() {
            return view('admin.reconciliation.index');
        })->name('reconciliation.index');

        // Quản lý Danh mục
        Route::get('/danh-muc', function() {
            return view('admin.categories.index');
        })->name('danh-muc.index');

        Route::get('/danh-muc/create', function() {
            return view('admin.categories.create');
        })->name('danh-muc.create');

        Route::get('/danh-muc/{id}/edit', function($id) {
            return view('admin.categories.edit', compact('id'));
        })->name('danh-muc.edit');

        Route::get('/danh-muc/{id}', function($id) {
            return view('admin.categories.show', compact('id'));
        })->name('danh-muc.show');

        // Cài đặt hệ thống
        Route::get('/settings', function() {
            return view('admin.settings.index');
        })->name('settings.index');

        // Manage Restaurants (Duyệt nhà hàng)
        Route::prefix('restaurants')->name('restaurants.')->group(function () {
            Route::get('/', function() {
                return view('admin.restaurants.index');
            })->name('index');
        });
    });
});

// Maintenance Takeover Route
Route::get('/maintenance', function () {
    return view('maintenance');
})->name('maintenance');

// Utility to clear cache via web browser
Route::get('/clear-cache-tafood-998877', function() {
    \Illuminate\Support\Facades\Artisan::call('optimize:clear');
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    return 'Cache and OPcache cleared successfully! Please reload the dashboard page.';
});

// Utility to view laravel logs on production via web browser
Route::get('/view-logs-tafood-998877', function() {
    $logPath = storage_path('logs/laravel.log');
    if (!file_exists($logPath)) {
        return 'No laravel.log file found.';
    }
    $lines = file($logPath);
    $lastLines = array_slice($lines, -1000);
    return '<pre>' . implode('', $lastLines) . '</pre>';
});