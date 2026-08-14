<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin\AdController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ContentPageController;
use App\Http\Controllers\Admin\EmailAdvertisementController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Admin\MockTestController as AdminMockTestController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\SectionController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\SubSectionController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CgiClipMediaController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\HazardMockTestController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MarketingEmailPreferenceController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\TheoryTestController;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about-us', [PageController::class, 'about'])->name('about');
Route::get('/contact-us', [PageController::class, 'contact'])->name('contact');
Route::get('/email/unsubscribe/{user}', [MarketingEmailPreferenceController::class, 'show'])
    ->middleware('signed')
    ->name('marketing.unsubscribe.show');
Route::post('/email/unsubscribe/{user}', [MarketingEmailPreferenceController::class, 'unsubscribe'])
    ->middleware('signed')
    ->name('marketing.unsubscribe');

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
    Route::patch('/account/marketing-preferences', [AccountController::class, 'updateMarketingPreferences'])->name('account.marketing-preferences.update');
    Route::put('/account/password', [AccountController::class, 'updatePassword'])->name('account.password.update');
    Route::delete('/account', [AccountController::class, 'destroy'])->name('account.destroy');
});

// Dynamic Sections
Route::get('/section/{section}', [FrontendController::class, 'showSection'])->name('frontend.section');
Route::get('/sub-section/{subSection}', [FrontendController::class, 'showSubSection'])->name('frontend.sub_section');
Route::get('/category/{category}', [FrontendController::class, 'showCategory'])->name('frontend.category');
Route::get('/media/cgi-clips/{cgiClip}', CgiClipMediaController::class)->name('media.cgi-clips.stream');
Route::get('/hazard-learning/clips/{contentPage}', [TheoryTestController::class, 'hazardStudy'])->name('theory.hazard_study');
Route::get('/hazard-learning/{topic}', [TheoryTestController::class, 'hazardLibrary'])->name('theory.hazard_library');
Route::post('/hazard-learning/clips/{contentPage}/watched', [TheoryTestController::class, 'markHazardWatched'])
    ->middleware('auth:web')
    ->name('theory.hazard_watched');
Route::post('/hazard-learning/progress/sync', [TheoryTestController::class, 'syncHazardProgress'])
    ->middleware('auth:web')
    ->name('theory.hazard_progress.sync');

Route::prefix('theory-test-practice')->name('theory.')->group(function () {
    Route::get('/topic/{topic}', [TheoryTestController::class, 'practice'])->name('practice');
    Route::get('/result', [TheoryTestController::class, 'result'])->name('result');

    // Hazard Perception Mock Test Routes
    Route::get('/hazard-mock-test', [HazardMockTestController::class, 'info'])->name('hazard_mock_info');
    Route::get('/hazard-mock-test/start', [HazardMockTestController::class, 'start'])->name('hazard_mock_start');
    Route::post('/hazard-mock-test/result', [HazardMockTestController::class, 'submit'])->name('hazard_mock_submit');
    Route::get('/hazard-mock-test/result', [HazardMockTestController::class, 'result'])->name('hazard_mock_result');

    // Mock Test Routes
    Route::get('/mock-test/result', [TheoryTestController::class, 'mockTestResult'])->name('mock_test_result');
    Route::post('/mock-test/result', [TheoryTestController::class, 'submitMockTest'])->name('mock_test_submit');
    Route::get('/mock-test/{subSection}', [TheoryTestController::class, 'mockTestInfo'])->name('mock_test_info');
    Route::get('/mock-test/{subSection}/start', [TheoryTestController::class, 'mockTestStart'])->name('mock_test_start');
    Route::get('/dynamic-mock-test/{mockTest}', [TheoryTestController::class, 'dynamicMockInfo'])->name('dynamic_mock_info');
    Route::get('/dynamic-mock-test/{mockTest}/start', [TheoryTestController::class, 'dynamicMockStart'])->name('dynamic_mock_start');
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
        Route::resource('sections', SectionController::class);

        // SubSections
        Route::resource('sections.sub_sections', SubSectionController::class);

        // Categories (nested under sub_sections)
        Route::resource('sections.sub_sections.categories', CategoryController::class)->except(['index']);

        // Topics (flat route to handle polymorphic creation/editing)
        Route::resource('topics', TopicController::class)->except(['show']);
        Route::resource('questions', QuestionController::class);
        Route::resource('content-pages', ContentPageController::class)->except('show');
        Route::resource('languages', LanguageController::class)->except('show');
        Route::resource('mock-tests', AdminMockTestController::class)->except('show');
        Route::get('site-settings', [SiteSettingController::class, 'edit'])->name('site-settings.edit');
        Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update');
        Route::resource('users', AdminUserController::class)->only(['index', 'edit', 'update', 'destroy']);
        Route::put('users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
        Route::get('csrf-token', fn () => response()->json(['token' => csrf_token()]))->name('csrf-token');

        // Advertisements
        Route::resource('ads', AdController::class);
        Route::patch('ads/{ad}/toggle-status', [AdController::class, 'toggleStatus'])->name('ads.toggle-status');
        Route::get('email-advertisements', [EmailAdvertisementController::class, 'create'])->name('email-advertisements.create');
        Route::post('email-advertisements', [EmailAdvertisementController::class, 'send'])->name('email-advertisements.send');
    });
});
