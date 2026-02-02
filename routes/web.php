<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MonAnController;
use App\Http\Controllers\NhaHangController;

// Homepage Route
Route::get('/', [MonAnController::class, 'menu'])->name('foods.index');

// Food Detail Route
Route::get('/food/{id}', [MonAnController::class, 'detail'])->name('food.detail');

// Restaurant Detail Route
Route::get('/restaurant/{id}', [NhaHangController::class, 'show'])->name('restaurants.show');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');