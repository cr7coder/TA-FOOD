<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonAnController;
use App\Http\Controllers\NhaHangController;
use App\Http\Controllers\GioHangController;

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

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');