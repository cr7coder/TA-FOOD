<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\FoodDetailController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\CartController;

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
