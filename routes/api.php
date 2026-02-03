<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FoodController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\FoodDetailController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CheckoutController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\PaymentController;

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
    Route::delete('/profile', [ProfileController::class, 'destroy']);
});

// Routes for Web (without v1 prefix) - for web interface with web session
Route::middleware('web')->group(function () {
    Route::post('/checkout/process-payment/{orderId}', [CheckoutController::class, 'processPayment']);
    
    // Profile routes for web session authentication
    Route::prefix('v1')->middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        Route::put('/profile/password', [ProfileController::class, 'updatePassword']);
        Route::delete('/profile', [ProfileController::class, 'destroy']);
    });
});
