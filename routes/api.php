<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\User\AuthController;
use App\Http\Controllers\Api\User\FoodController;
use App\Http\Controllers\Api\User\HomeController;
use App\Http\Controllers\Api\User\FoodDetailController;
use App\Http\Controllers\Api\User\RestaurantController;
use App\Http\Controllers\Api\User\CartController;
use App\Http\Controllers\Api\User\CheckoutController;
use App\Http\Controllers\Api\User\ProfileController;
use App\Http\Controllers\Api\User\OrderController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\User\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes (không cần authentication)
Route::prefix('v1')->group(function () {
    // Authentication routes
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Food routes (public)
    Route::get('/foods', [FoodController::class, 'index']);
    Route::get('/foods/categories', [FoodController::class, 'categories']);
    Route::get('/foods/{id}', [FoodController::class, 'show']);

    // Forgot Password OTP routes
    Route::post('/forgot-password/send-otp', [AuthController::class, 'sendForgotPasswordOtp']);
    Route::post('/forgot-password/verify-otp', [AuthController::class, 'verifyForgotPasswordOtp']);
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword']);
});

// Protected routes (cần authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    });
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Logout from all devices
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
});

// Home routes
Route::prefix('v1')->group(function () {
    // Foods
    Route::get('/foods', [HomeController::class, 'foods']);
    Route::get('/foods/{id}', [FoodDetailController::class, 'show']);
    
    // Restaurants
    Route::get('/restaurants', [HomeController::class, 'restaurants']);
    Route::get('/restaurants/{id}', [RestaurantController::class, 'show']);
    Route::get('/restaurants/{id}/foods', [RestaurantController::class, 'foods']);
    
    // Offers & Reviews
    Route::get('/offers', [HomeController::class, 'offers']);
    Route::get('/reviews', [HomeController::class, 'reviews']);
    
    // Cart (không yêu cầu auth, hỗ trợ guest)
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);
});

// Protected routes - Food Reviews (yêu cầu auth)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::post('/foods/{id}/reviews', [FoodDetailController::class, 'storeReview']);
});

// Checkout & Orders routes (yêu cầu auth)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Checkout
    Route::get('/checkout', [CheckoutController::class, 'getCheckoutInfo']);
    Route::post('/checkout/apply-voucher', [CheckoutController::class, 'applyVoucher']);
    Route::post('/checkout/create-order', [CheckoutController::class, 'createOrder']);
    Route::post('/checkout/process-payment/{orderId}', [CheckoutController::class, 'processPayment']);
    
    // Orders - RESTful API
    Route::get('/orders', [OrderController::class, 'index']); // Danh sách đơn hàng
    Route::get('/orders/{id}', [OrderController::class, 'show']); // Chi tiết đơn hàng
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancel']); // Hủy đơn hàng
    
    // Payments - RESTful API
    Route::get('/payments', [PaymentController::class, 'index']); // Danh sách thanh toán
    Route::get('/payments/{id}', [PaymentController::class, 'show']); // Chi tiết thanh toán
    Route::post('/payments', [PaymentController::class, 'store']); // Tạo thanh toán mới
    Route::put('/payments/{id}', [PaymentController::class, 'update']); // Cập nhật trạng thái thanh toán
    Route::get('/payments/check/{orderId}', [PaymentController::class, 'checkPaymentStatus']); // Kiểm tra trạng thái thanh toán
    
    // Reviews - RESTful API
    Route::get('/reviews', [ReviewController::class, 'index']); // Danh sách đánh giá của user
    Route::post('/reviews', [ReviewController::class, 'store']); // Tạo đánh giá mới
    Route::get('/reviews/check/{orderId}/{foodId}', [ReviewController::class, 'checkReviewed']); // Kiểm tra đã đánh giá
    
    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::put('/profile/tag-address', [ProfileController::class, 'updateTagAddress']);
    Route::delete('/profile', [ProfileController::class, 'destroy']);
});

// Routes for Web (without v1 prefix) - for web interface with web session
Route::middleware('web')->group(function () {
    Route::post('/checkout/process-payment/{orderId}', [CheckoutController::class, 'processPayment']);
    
    // Profile, Orders, Reviews routes for web session authentication
    Route::prefix('v1')->middleware('auth')->group(function () {
        // Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::put('/profile/tag-address', [ProfileController::class, 'updateTagAddress']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
        
        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        
        // Reviews
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/check/{orderId}/{foodId}', [ReviewController::class, 'checkReviewed']);
        
        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Api\User\NotificationController::class, 'index']);
        Route::post('/notifications/read-all', [\App\Http\Controllers\Api\User\NotificationController::class, 'readAll']);
        Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\User\NotificationController::class, 'read']);
        
            // Seller Dashboard API
            Route::prefix('seller')->middleware('role:NguoiBan')->group(function () {
                Route::get('/dashboard', [\App\Http\Controllers\Api\Seller\DashboardController::class, 'index']);
                
                // Foods
                Route::get('/foods', [\App\Http\Controllers\Api\Seller\FoodController::class, 'index'])->name('api.seller.foods.index');
                Route::get('/foods/{id}', [\App\Http\Controllers\Api\Seller\FoodController::class, 'show'])->name('api.seller.foods.show');
                Route::post('/foods', [\App\Http\Controllers\Api\Seller\FoodController::class, 'store'])->name('api.seller.foods.store');
                Route::put('/foods/{id}', [\App\Http\Controllers\Api\Seller\FoodController::class, 'update'])->name('api.seller.foods.update');
                Route::delete('/foods/{id}', [\App\Http\Controllers\Api\Seller\FoodController::class, 'destroy'])->name('api.seller.foods.destroy');
                Route::get('/foods/check-name', [\App\Http\Controllers\Api\Seller\FoodController::class, 'checkName'])->name('api.seller.foods.check-name');

                // Restaurant
                Route::get('/restaurant', [\App\Http\Controllers\Api\Seller\RestaurantController::class, 'show'])->name('api.seller.restaurant.show');
                Route::put('/restaurant', [\App\Http\Controllers\Api\Seller\RestaurantController::class, 'update'])->name('api.seller.restaurant.update');
                Route::post('/restaurant/resolve-url', [\App\Http\Controllers\Api\Seller\RestaurantController::class, 'resolveMapUrl'])->name('api.seller.restaurant.resolve-url');

                // Orders
                Route::get('/orders', [\App\Http\Controllers\Api\Seller\OrderController::class, 'index'])->name('api.seller.orders.index');
                Route::get('/orders/{id}', [\App\Http\Controllers\Api\Seller\OrderController::class, 'show'])->name('api.seller.orders.show');
                Route::put('/orders/{id}/status', [\App\Http\Controllers\Api\Seller\OrderController::class, 'updateStatus'])->name('api.seller.orders.update-status');
                Route::put('/orders/{id}/carrier', [\App\Http\Controllers\Api\Seller\OrderController::class, 'updateCarrier'])->name('api.seller.orders.update-carrier');

                // Reports
                Route::get('/reports', [\App\Http\Controllers\Api\Seller\ReportController::class, 'index'])->name('api.seller.reports.index');

                // Reviews
                Route::get('/reviews', [\App\Http\Controllers\Api\Seller\ReviewController::class, 'index'])->name('api.seller.reviews.index');
            });

            // Admin API
            Route::prefix('admin')->middleware('role:QuanTri')->group(function () {
                // Dashboard
                Route::get('/dashboard', [\App\Http\Controllers\Api\Admin\DashboardController::class, 'index'])->name('api.admin.dashboard');

                // Báo cáo
                Route::get('/reports', [\App\Http\Controllers\Api\Admin\ReportController::class, 'index'])->name('api.admin.reports');

                // Đối soát tài chính
                Route::get('/reconciliation', [\App\Http\Controllers\Api\Admin\ReconciliationController::class, 'index'])->name('api.admin.reconciliation.index');
                Route::post('/reconciliation/payout', [\App\Http\Controllers\Api\Admin\ReconciliationController::class, 'payout'])->name('api.admin.reconciliation.payout');

                // Đối tác vận chuyển
                Route::get('/doi-tac-van-chuyen', [\App\Http\Controllers\Api\Admin\DoiTacVanChuyenController::class, 'index'])->name('api.admin.doi-tac-van-chuyen.index');
                Route::post('/doi-tac-van-chuyen', [\App\Http\Controllers\Api\Admin\DoiTacVanChuyenController::class, 'store'])->name('api.admin.doi-tac-van-chuyen.store');
                Route::get('/doi-tac-van-chuyen/{id}', [\App\Http\Controllers\Api\Admin\DoiTacVanChuyenController::class, 'show'])->name('api.admin.doi-tac-van-chuyen.show');
                Route::put('/doi-tac-van-chuyen/{id}', [\App\Http\Controllers\Api\Admin\DoiTacVanChuyenController::class, 'update'])->name('api.admin.doi-tac-van-chuyen.update');
                Route::delete('/doi-tac-van-chuyen/{id}', [\App\Http\Controllers\Api\Admin\DoiTacVanChuyenController::class, 'destroy'])->name('api.admin.doi-tac-van-chuyen.destroy');

                // Users
                Route::get('/users', [\App\Http\Controllers\Api\Admin\UserController::class, 'index'])->name('api.admin.users.index');
                Route::get('/users/stats', [\App\Http\Controllers\Api\Admin\UserController::class, 'stats'])->name('api.admin.users.stats');
                Route::post('/users', [\App\Http\Controllers\Api\Admin\UserController::class, 'store'])->name('api.admin.users.store');
                Route::get('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'show'])->name('api.admin.users.show');
                Route::post('/users/{id}/toggle-status', [\App\Http\Controllers\Api\Admin\UserController::class, 'toggleStatus'])->name('api.admin.users.toggle-status');
                Route::put('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'update'])->name('api.admin.users.update');
                Route::delete('/users/{id}', [\App\Http\Controllers\Api\Admin\UserController::class, 'destroy'])->name('api.admin.users.destroy');

                // Orders
                Route::get('/orders', [\App\Http\Controllers\Api\Admin\OrderController::class, 'index'])->name('api.admin.orders.index');
                Route::get('/orders/stats', [\App\Http\Controllers\Api\Admin\OrderController::class, 'stats'])->name('api.admin.orders.stats');
                Route::get('/orders/{id}', [\App\Http\Controllers\Api\Admin\OrderController::class, 'show'])->name('api.admin.orders.show');
                Route::put('/orders/{id}/status', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateStatus'])->name('api.admin.orders.update-status');
                Route::put('/orders/{id}/carrier', [\App\Http\Controllers\Api\Admin\OrderController::class, 'updateCarrier'])->name('api.admin.orders.update-carrier');
                Route::post('/orders/{id}/confirm-payment', [\App\Http\Controllers\Api\Admin\OrderController::class, 'confirmPayment'])->name('api.admin.orders.confirm-payment');

                // Admin Notifications (dùng chung NotificationService theo auth user)
                Route::get('/notifications', [\App\Http\Controllers\Api\User\NotificationController::class, 'index'])->name('api.admin.notifications.index');
                Route::post('/notifications/read-all', [\App\Http\Controllers\Api\User\NotificationController::class, 'readAll'])->name('api.admin.notifications.read-all');
                Route::post('/notifications/{id}/read', [\App\Http\Controllers\Api\User\NotificationController::class, 'read'])->name('api.admin.notifications.read');

                // Vouchers
                Route::get('/vouchers', [\App\Http\Controllers\Api\Admin\VoucherController::class, 'index'])->name('api.admin.vouchers.index');
                Route::post('/vouchers', [\App\Http\Controllers\Api\Admin\VoucherController::class, 'store'])->name('api.admin.vouchers.store');
                Route::get('/vouchers/{id}', [\App\Http\Controllers\Api\Admin\VoucherController::class, 'show'])->name('api.admin.vouchers.show');
                Route::put('/vouchers/{id}', [\App\Http\Controllers\Api\Admin\VoucherController::class, 'update'])->name('api.admin.vouchers.update');
                Route::delete('/vouchers/{id}', [\App\Http\Controllers\Api\Admin\VoucherController::class, 'destroy'])->name('api.admin.vouchers.destroy');

                // Danh mục
                Route::get('/danh-muc', [\App\Http\Controllers\Api\Admin\DanhMucController::class, 'index'])->name('api.admin.danh-muc.index');
                Route::post('/danh-muc', [\App\Http\Controllers\Api\Admin\DanhMucController::class, 'store'])->name('api.admin.danh-muc.store');
                Route::get('/danh-muc/{id}', [\App\Http\Controllers\Api\Admin\DanhMucController::class, 'show'])->name('api.admin.danh-muc.show');
                Route::put('/danh-muc/{id}', [\App\Http\Controllers\Api\Admin\DanhMucController::class, 'update'])->name('api.admin.danh-muc.update');
                Route::delete('/danh-muc/{id}', [\App\Http\Controllers\Api\Admin\DanhMucController::class, 'destroy'])->name('api.admin.danh-muc.destroy');

                // Cài đặt hệ thống
                Route::get('/settings', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'show'])->name('api.admin.settings.show');
                Route::post('/settings', [\App\Http\Controllers\Api\Admin\SettingsController::class, 'update'])->name('api.admin.settings.update');

                // Manage Restaurants
                Route::get('/restaurants', [\App\Http\Controllers\Api\Admin\RestaurantController::class, 'index'])->name('api.admin.restaurants.index');
                Route::put('/restaurants/{id}/status', [\App\Http\Controllers\Api\Admin\RestaurantController::class, 'updateStatus'])->name('api.admin.restaurants.update-status');
            });
    });
});

// Cổng thanh toán PayOS Webhook (Không yêu cầu Authentication/CSRF)
Route::post('/payos/webhook', [\App\Http\Controllers\Api\Webhook\PayOSWebhookController::class, 'handleWebhook']);

