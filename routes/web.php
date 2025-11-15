<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    ProductController,
    ProfileController,
    UserController,
    OwnerController,
    AdminProduksiController,
    AdminPenjualanController,
    DaftarProdukController,
    CategoryController,
    SalesController,
    SalesReportController,
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
    Route::get('/owner/dashboard', [OwnerController::class, 'index'])->name('dashboard'); 
    Route::resource('users', UserController::class);

    //prduk
    Route::get('/owner/daftar/produk', [DaftarProdukController::class, 'index'])->name('owner.produk.index');
    Route::post('/owner/daftar/produk/store', [DaftarProdukController::class, 'store'])->name('owner.produk.store');
    Route::put('/owner/daftar/produk/update/{produk}', [DaftarProdukController::class, 'update'])->name('owner.produk.update');
    Route::delete('/owner/daftar/produk/destroy/{produk}', [DaftarProdukController::class, 'destroy'])->name('owner.produk.destroy');
});

// ADMIN PRODUKSI
Route::middleware(['auth', 'verified', 'role:admin_produksi'])->group(function () {
    Route::get('/admin/produksi/dashboard', [AdminProduksiController::class, 'dashboard'])->name('produksi.dashboard');

    //validasi req konsinyasi user
    Route::get('/admin/produksi/konsinyasi', [AdminProduksiController::class, 'adminIndex'])->name('admin.konsinyasi.index');

    Route::post('/admin/produksi/konsinyasi/{id}/status', [AdminProduksiController::class, 'updateStatus'])->name('admin.konsinyasi.status');
    
    Route::get('/admin/produksi/riwayat-disetujui', [AdminProduksiController::class, 'riwayatDisetujui'])->name('admin.konsinyasi.riwayat.disetujui');
    Route::get('/admin/produksi/riwayat-ditolak', [AdminProduksiController::class, 'riwayatDitolak'])->name('admin.konsinyasi.riwayat.ditolak');

});

// ADMIN PENJUALAN
Route::middleware(['auth', 'verified', 'role:admin_penjualan'])->group(function () {
    Route::get('admin/penjualan/dashboard', [AdminPenjualanController::class, 'index'])->name('penjualan.dashboard');
});

// SALES
Route::middleware(['auth', 'verified', 'role:sales'])->group(function () {
    Route::get('/sales/reports', [SalesReportController::class, 'index'])->name('sales.reports.index');
    Route::get('/sales/reports/pdf', [SalesReportController::class, 'exportPdf'])->name('sales.reports.pdf');
    Route::resource('sales', SalesController::class);
   });


// USER
Route::middleware(['auth', 'verified', 'role:user'])->group(function () {
    Route::get('/user/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

});

//owner dan admin produksi
Route::middleware(['auth', 'verified', 'role:owner|admin_produksi'])->group(function () {
    //kategori prodk
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

// USER
Route::middleware(['auth'])->group(function () {
    // Konsinyasi produk user
    Route::get('/consignments/create', [ProductController::class, 'create'])->name('consignments.create');
    Route::post('/consignments/submit', [ProductController::class, 'submit'])->name('consignments.submit');
    Route::get('/consignments/history', [ProductController::class, 'history'])->name('consignments.history');

    //ajukan kembaliii
    Route::post('/products/{product}/resubmit', [ProductController::class, 'resubmit'])->name('products.resubmit');
    Route::post('/products/{product}/cancel', [ProductController::class, 'cancel'])->name('products.cancel');

});


require __DIR__.'/auth.php';
