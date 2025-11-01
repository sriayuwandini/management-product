<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProfileController,
    UserController,
    OwnerController,
    AdminProduksiController,
    AdminPenjualanController,
    SalesController,
    UserDashboardController
};

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'verified', 'role:owner'])->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'index'])->name('dashboard'); 
    Route::resource('users', UserController::class);
});

Route::middleware(['auth', 'verified', 'role:admin_produksi'])->group(function () {
    Route::get('/produksi', [AdminProduksiController::class, 'index'])->name('produksi.dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin_penjualan'])->group(function () {
    Route::get('/penjualan', [AdminPenjualanController::class, 'index'])->name('penjualan.dashboard');
});

Route::middleware(['auth', 'verified', 'role:sales'])->group(function () {
    Route::get('/sales', [SalesController::class, 'index'])->name('sales.dashboard');
});

Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/home', [UserDashboardController::class, 'index'])->name('home');
});

require __DIR__.'/auth.php';
