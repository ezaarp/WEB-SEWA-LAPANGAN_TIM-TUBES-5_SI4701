<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ApprovalController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\FacilityTypeController;

// Public routes
Route::get('/', function () {
    return redirect('/login');
});

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'registerWeb'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

// Protected routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Facilities (all users can view, only admin can manage)
    Route::resource('facilities', FacilityController::class);
    Route::get('/api/facilities/available', [FacilityController::class, 'available'])->name('facilities.available');
    
    // Bookings (mahasiswa can create, all can view their own)
    Route::resource('bookings', BookingController::class);
    
    // Admin only routes
    Route::middleware('role:admin')->group(function () {
        Route::resource('areas', AreaController::class);
        Route::resource('facility-types', FacilityTypeController::class);
        
        // Admin approval routes
        Route::get('/admin/approvals', [ApprovalController::class, 'adminIndex'])->name('admin.approvals.index');
        Route::post('/admin/approvals/{booking}', [ApprovalController::class, 'adminApprove'])->name('admin.approvals.approve');
        Route::post('/admin/approvals/{booking}/verify', [ApprovalController::class, 'adminVerify'])->name('admin.approvals.verify');
        Route::post('/admin/approvals/{booking}/complete', [ApprovalController::class, 'adminComplete'])->name('admin.approvals.complete');
        
        // Payment management
        Route::get('/admin/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
        Route::post('/admin/payments/{payment}/verify', [PaymentController::class, 'verify'])->name('admin.payments.verify');
        Route::get('/admin/payments/{payment}/proof', [PaymentController::class, 'showProof'])->name('admin.payments.proof');
    });
    
    // Penanggung Jawab routes
    Route::middleware('role:penanggung_jawab')->group(function () {
        Route::get('/pj/approvals', [ApprovalController::class, 'penanggungjawabIndex'])->name('pj.approvals.index');
        Route::get('/pj/approvals/{booking}', [ApprovalController::class, 'penanggungjawabShow'])->name('pj.approvals.show');
        Route::post('/pj/approvals/{booking}', [ApprovalController::class, 'penanggungjawabApprove'])->name('pj.approvals.approve');
    });
    
    // Mahasiswa routes
    Route::middleware('role:mahasiswa')->group(function () {
        Route::get('/my-bookings', [BookingController::class, 'index'])->name('my.bookings');
        Route::post('/payments/{payment}/upload', [PaymentController::class, 'uploadProof'])->name('payments.upload');
        Route::get('/payments/{payment}/proof', [PaymentController::class, 'showProofMahasiswa'])->name('payments.proof');
    });
});
