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

            // route satuan
            Route::get('/satuan', [adminController::class, 'dataSatuan'])->name('dashboard.satuan');
            Route::get('/satuan/tambah', [adminController::class, 'createSatuan'])->name('satuan.create');
            Route::post('/satuan', [adminController::class, 'storeSatuan'])->name('satuan.store');
            Route::get('/satuan/{id}/edit', [adminController::class, 'editSatuan'])->name('satuan.edit');
            Route::put('/satuan/{id}', [adminController::class, 'updateSatuan'])->name('satuan.update');
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
