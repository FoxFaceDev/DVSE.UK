<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TheoryTestController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuestionController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('theory-test-practice')->name('theory.')->group(function () {
    Route::get('/', [TheoryTestController::class, 'index'])->name('index');
    Route::get('/categories', [TheoryTestController::class, 'categories'])->name('categories');
    Route::get('/category/{category}', [TheoryTestController::class, 'practice'])->name('practice');
    Route::get('/result', [TheoryTestController::class, 'result'])->name('result');
});

// Admin Authentication Routes (Optional to be protected by auth middleware for now, we will just create the structure)
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('home');
    Route::resource('categories', CategoryController::class);
    Route::resource('questions', QuestionController::class);
});
