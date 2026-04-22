<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\MemberController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Member\DashboardController as MemberDashboardController;
use App\Http\Controllers\Member\PackageController as MemberPackageController;
use App\Http\Controllers\Member\PaymentController as MemberPaymentController;
use App\Http\Controllers\Member\ProgressController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// ========================================================================
// PUBLIC ROUTES
// ========================================================================

Route::get('/', function () {
    return view('home');
})->name('home');

// ========================================================================
// AUTH ROUTES
// ========================================================================

Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    
    // Register
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Logout (requires authentication)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// ========================================================================
// ADMIN ROUTES
// ========================================================================

Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    
    // Members
    Route::get('/members', [MemberController::class, 'index'])->name('members');
    
    // Packages
    Route::get('/packages', [AdminPackageController::class, 'index'])->name('packages');
    Route::post('/packages', [AdminPackageController::class, 'store'])->name('packages.store');
    Route::put('/packages/{id}', [AdminPackageController::class, 'update'])->name('packages.update');
    Route::delete('/packages/{id}', [AdminPackageController::class, 'destroy'])->name('packages.destroy');
    
    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments');
    Route::post('/payments/{id}/confirm', [AdminPaymentController::class, 'confirm'])->name('payments.confirm');
    
});

// ========================================================================
// MEMBER ROUTES
// ========================================================================

Route::prefix('member')->middleware(['auth', 'member'])->name('member.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [MemberDashboardController::class, 'index'])->name('dashboard');
    
    // Packages
    Route::get('/packages', [MemberPackageController::class, 'index'])->name('packages');
    Route::get('/checkout', [MemberPackageController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [MemberPackageController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/payment-success/{id}', [MemberPackageController::class, 'paymentSuccess'])->name('payment.success');
    
    // Payments History
    Route::get('/payments', [MemberPaymentController::class, 'index'])->name('payments');
    
    // Progress Tracking
    Route::get('/progress', [ProgressController::class, 'index'])->name('progress');
    Route::post('/progress', [ProgressController::class, 'store'])->name('progress.store');
    Route::put('/progress/{id}', [ProgressController::class, 'update'])->name('progress.update');
    Route::delete('/progress/{id}', [ProgressController::class, 'destroy'])->name('progress.destroy');
    
});