<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\TheoryTestController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\EmailAdvertisementController;

use App\Http\Controllers\Admin\LoginController as AdminLoginController;

use App\Http\Controllers\FrontendController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');

// User Authentication Routes
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [PasswordResetController::class, 'request'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'email'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'reset'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth:web')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('/account', [AccountController::class, 'show'])->name('account.show');
    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});

// Dynamic Sections
Route::get('/section/{section}', [FrontendController::class, 'showSection'])->name('frontend.section');
Route::get('/sub-section/{subSection}', [FrontendController::class, 'showSubSection'])->name('frontend.sub_section');

Route::prefix('theory-test-practice')->name('theory.')->group(function () {
    Route::get('/category/{category}', [TheoryTestController::class, 'practice'])->name('practice');
    Route::get('/result', [TheoryTestController::class, 'result'])->name('result');

    // Mock Test Routes
    Route::get('/mock-test/result', [TheoryTestController::class, 'mockTestResult'])->name('mock_test_result');
    Route::post('/mock-test/result', [TheoryTestController::class, 'submitMockTest'])->name('mock_test_submit');
    Route::get('/mock-test/{subSection}', [TheoryTestController::class, 'mockTestInfo'])->name('mock_test_info');
    Route::get('/mock-test/{subSection}/start', [TheoryTestController::class, 'mockTestStart'])->name('mock_test_start');
});

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/my-history', [TheoryTestController::class, 'history'])->name('history');
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
        Route::resource('content-pages', ContentPageController::class)->except('show');

        // Advertisements
        Route::resource('ads', AdController::class);
        Route::patch('ads/{ad}/toggle-status', [AdController::class, 'toggleStatus'])->name('ads.toggle-status');
        Route::get('email-advertisements', [EmailAdvertisementController::class, 'create'])->name('email-advertisements.create');
        Route::post('email-advertisements', [EmailAdvertisementController::class, 'send'])->name('email-advertisements.send');
    });
});
