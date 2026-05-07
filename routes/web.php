<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TheoryTestController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuestionController;

use App\Http\Controllers\Admin\LoginController as AdminLoginController;

use App\Http\Controllers\FrontendController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// Dynamic Sections
Route::get('/section/{section}', [FrontendController::class, 'showSection'])->name('frontend.section');
Route::get('/sub-section/{subSection}', [FrontendController::class, 'showSubSection'])->name('frontend.sub_section');

Route::prefix('theory-test-practice')->name('theory.')->group(function () {
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
        
        // Sections
        Route::resource('sections', App\Http\Controllers\Admin\SectionController::class);
        
        // SubSections
        Route::resource('sections.sub_sections', App\Http\Controllers\Admin\SubSectionController::class);
        
        // Categories (nested under sub_sections)
        Route::resource('sections.sub_sections.categories', CategoryController::class)->except(['index']);
        
        // We will keep a generic categories fallback if needed, but preferably they should go through the tree.
        Route::resource('questions', QuestionController::class);
    });
});
