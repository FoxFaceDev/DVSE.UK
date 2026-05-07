<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TheoryTestController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuestionController;

use App\Http\Controllers\Admin\LoginController as AdminLoginController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('theory-test-practice')->name('theory.')->group(function () {
    Route::get('/', [TheoryTestController::class, 'index'])->name('index');
    Route::get('/categories', [TheoryTestController::class, 'categories'])->name('categories');
    Route::get('/category/{category}', [TheoryTestController::class, 'practice'])->name('practice');
    Route::get('/result', [TheoryTestController::class, 'result'])->name('result');
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes for admin
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login']);
    });

    // Protected admin routes
    Route::middleware('auth:admin')->group(function () {
        Route::post('/logout', [AdminLoginController::class, 'logout'])->name('logout');
        Route::get('/', [AdminController::class, 'index'])->name('home');
        Route::resource('categories', CategoryController::class);
        Route::resource('questions', QuestionController::class);
    });
});
