<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\gudangController;
use App\Http\Controllers\kasirController;
use App\Http\Controllers\managerController;
use App\Http\Controllers\penjualanController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::redirect('/', '/login', 301);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

// Protected Routes (Harus Login)
Route::middleware([AuthMiddleware::class])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware([RoleMiddleware::class.':admin'])
        ->prefix('dashboard/admin')
        ->as('admin.')
        ->group(function () {
            Route::get('/', [adminController::class, 'index'])->name('dashboard');
            Route::post('/margin', [adminController::class, 'updateMargin'])->name('dashboard.margin.update');

            // Route Barang
            Route::get('/barang', [adminController::class, 'dataBarang'])->name('dashboard.barang');
            Route::get('/barang/tambah', [adminController::class, 'createBarang'])->name('barang.create');
            Route::post('/barang', [adminController::class, 'storeBarang'])->name('barang.store');
            Route::get('/barang/{id}/edit', [adminController::class, 'editBarang'])->name('barang.edit');
            Route::put('barang/{id}', [adminController::class, 'updateBarang'])->name('barang.update');
            Route::delete('barang/{id}', [adminController::class, 'destroyBarang'])->name('barang.destroy');

            // route satuan
            Route::get('/satuan', [adminController::class, 'dataSatuan'])->name('dashboard.satuan');
            Route::get('/satuan/tambah', [adminController::class, 'createSatuan'])->name('satuan.create');
            Route::post('/satuan', [adminController::class, 'storeSatuan'])->name('satuan.store');
            Route::get('/satuan/{id}/edit', [adminController::class, 'editSatuan'])->name('satuan.edit');
            Route::put('/satuan/{id}', [adminController::class, 'updateSatuan'])->name('satuan.update');
            Route::delete('satuan/{id}', [adminController::class, 'destroySatuan'])->name('satuan.destroy');

            // route vendor
            Route::get('/vendor', [adminController::class, 'dataVendor'])->name('dashboard.vendor');
            Route::get('/vendor/tambah', [adminController::class, 'createVendor'])->name('vendor.create');
            Route::post('/vendor', [adminController::class, 'storeVendor'])->name('vendor.store');
            Route::get('/vendor/{id}/edit', [adminController::class, 'editVendor'])->name('vendor.edit');
            Route::put('/vendor/{id}', [adminController::class, 'updateVendor'])->name('vendor.update');
            Route::delete('/vendor/{id}', [adminController::class, 'destroyVendor'])->name('vendor.destroy');

            //user route
            Route::get('/user', [adminController::class, 'dataUser'])->name('dashboard.vendor');
            Route::get('/user/tambah', [adminController::class, 'createUser'])->name('user.create');
            Route::post('/user', [adminController::class, 'storeUser'])->name('user.store');
            Route::get('/user/{id}/edit', [adminController::class, 'editUser'])->name('user.edit');
            Route::put('/user/{id}', [adminController::class, 'updateUser'])->name('user.update');
            Route::delete('/user/{id}', [adminController::class, 'destroyUser'])->name('user.destroy');
        });

    // Manager Route
    Route::get('/dashboard/manager', [managerController::class, 'index'])
        ->name('manager.dashboard')
        ->middleware(RoleMiddleware::class.':manager');

    // Kasir Route
    Route::get('/dashboard/kasir', [kasirController::class, 'index'])
        ->name('kasir.dashboard')
        ->middleware(RoleMiddleware::class.':kasir');

    // Gudang Route
    Route::get('/dashboard/gudang', [gudangController::class, 'index'])
        ->name('gudang.dashboard')
        ->middleware(RoleMiddleware::class.':petugas-gudang');

    // Penjualan Route
    Route::get('/dashboard/penjualan', [penjualanController::class, 'index'])
        ->name('penjualan.dashboard')
        ->middleware(RoleMiddleware::class.':staff-penjualan');
});
