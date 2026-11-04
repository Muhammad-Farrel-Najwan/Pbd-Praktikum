<?php

use App\Http\Controllers\Admin\BarangController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PenerimaanController;
use App\Http\Controllers\Admin\PengadaanController;
use App\Http\Controllers\Admin\PenjualanController as AdminPenjualanController;
use App\Http\Controllers\Admin\ReturController;
use App\Http\Controllers\Admin\SatuanController;
use App\Http\Controllers\Admin\StokController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\gudangController;
use App\Http\Controllers\Kasir\DashboardController as KasirDashboardController;
use App\Http\Controllers\Kasir\PenjualanController as KasirPenjualanController;
use App\Http\Controllers\managerController;
use App\Http\Controllers\penjualanController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

// Public Routes
Route::redirect('/', '/login', 301);
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');
Route::post(
    '/login',
    [AuthController::class, 'login']
)->name('login.process');

// Protected Routes (Harus Login)
Route::middleware([AuthMiddleware::class])->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Admin Routes
    Route::middleware([RoleMiddleware::class.':admin'])
        ->prefix('dashboard/admin')
        ->as('admin.')
        ->group(function () {
            Route::get('/', [DashboardController::class, 'index'])
                ->name('dashboard');
            Route::post('/margin', [DashboardController::class, 'updateMargin'])
                ->name('dashboard.margin.update');

            // Route Barang
            Route::get('/barang', [BarangController::class, 'dataBarang'])
                ->name('dashboard.barang');
            Route::get('/barang/tambah', [BarangController::class, 'createBarang'])
                ->name('barang.create');
            Route::post('/barang', [BarangController::class, 'storeBarang'])
                ->name('barang.store');
            Route::get('/barang/{id}/edit', [BarangController::class, 'editBarang'])
                ->name('barang.edit');
            Route::put('barang/{id}', [BarangController::class, 'updateBarang'])
                ->name('barang.update');
            Route::delete('barang/{id}', [BarangController::class, 'destroyBarang'])
                ->name('barang.destroy');

            // route satuan
            Route::get('/satuan', [SatuanController::class, 'dataSatuan'])
                ->name('dashboard.satuan');
            Route::get('/satuan/tambah', [SatuanController::class, 'createSatuan'])
                ->name('satuan.create');
            Route::post('/satuan', [SatuanController::class, 'storeSatuan'])
                ->name('satuan.store');
            Route::get('/satuan/{id}/edit', [SatuanController::class, 'editSatuan'])
                ->name('satuan.edit');
            Route::put('/satuan/{id}', [SatuanController::class, 'updateSatuan'])
                ->name('satuan.update');
            Route::delete('satuan/{id}', [SatuanController::class, 'destroySatuan'])
                ->name('satuan.destroy');

            // route vendor
            Route::get('/vendor', [VendorController::class, 'dataVendor'])
                ->name('dashboard.vendor');
            Route::get('/vendor/tambah', [VendorController::class, 'createVendor'])
                ->name('vendor.create');
            Route::post('/vendor', [VendorController::class, 'storeVendor'])
                ->name('vendor.store');
            Route::get('/vendor/{id}/edit', [VendorController::class, 'editVendor'])
                ->name('vendor.edit');
            Route::put('/vendor/{id}', [VendorController::class, 'updateVendor'])
                ->name('vendor.update');
            Route::delete('/vendor/{id}', [VendorController::class, 'destroyVendor'])
                ->name('vendor.destroy');

            // stok route
            Route::get('/stok', [StokController::class, 'dataStok'])
                ->name('dashboard.stok');
            Route::get('/stok/{id}', [StokController::class, 'detailStok'])
                ->name('stok.detail');

            // user route
            Route::get('/user', [UserController::class, 'dataUser'])
                ->name('dashboard.user');
            Route::get('/user/tambah', [UserController::class, 'createUser'])
                ->name('user.create');
            Route::post('/user', [UserController::class, 'storeUser'])
                ->name('user.store');
            Route::get('/user/{id}/edit', [UserController::class, 'editUser'])
                ->name('user.edit');
            Route::put('/user/{id}', [UserController::class, 'updateUser'])
                ->name('user.update');
            Route::delete('/user/{id}', [UserController::class, 'destroyUser'])
                ->name('user.destroy');

            // pengadaan route
            Route::get('/pengadaan', [PengadaanController::class, 'dataPengadaan'])
                ->name('dashboard.pengadaan');
            Route::get('/pengadaan/tambah', [PengadaanController::class, 'createPengadaan'])
                ->name('pengadaan.create');
            Route::post('/pengadaan', [PengadaanController::class, 'storePengadaan'])
                ->name('pengadaan.store');
            Route::get('/pengadaan/{id}', [PengadaanController::class, 'detailPengadaan'])
                ->name('pengadaan.detail');

            // penerimaan route
            Route::get('/penerimaan', [PenerimaanController::class, 'dataPenerimaan'])
                ->name('dashboard.penerimaan');
            Route::get('/penerimaan/tambah', [PenerimaanController::class, 'createPenerimaan'])
                ->name('penerimaan.create');
            Route::post('/penerimaan', [PenerimaanController::class, 'storePenerimaan'])
                ->name('penerimaan.store');
            Route::get('/penerimaan/{id}', [PenerimaanController::class, 'detailPenerimaan'])
                ->name('penerimaan.detail');

            // retur route
            Route::get('/retur', [ReturController::class, 'dataRetur'])
                ->name('dashboard.retur');

            Route::get('/retur/tambah', [ReturController::class, 'createRetur'])
                ->name('retur.create');

            Route::post('/retur', [ReturController::class, 'storeRetur'])
                ->name('retur.store');

            Route::get('/retur/{id}', [ReturController::class, 'detailRetur'])
                ->name('retur.detail');

            Route::post('/retur/{id}/proses', [ReturController::class, 'prosesRetur'])
                ->name('retur.proses');

            // penjualan route
            Route::get('/penjualan', [AdminPenjualanController::class, 'dataPenjualan'])
                ->name('penjualan.index');
            Route::get('/penjualan/create', [AdminPenjualanController::class, 'createPenjualan'])
                ->name('penjualan.create');
            Route::post('/penjualan', [AdminPenjualanController::class, 'storePenjualan'])
                ->name('penjualan.store');
            Route::get('/penjualan/{id}/struk', [AdminPenjualanController::class, 'strukPenjualan'])
                ->name('penjualan.struk');
            Route::get('/penjualan/{id}', [AdminPenjualanController::class, 'detailPenjualan'])
                ->name('penjualan.detail');
        });

    // Kasir Route
    Route::middleware(RoleMiddleware::class.':kasir')
        ->prefix('dashboard/kasir')
        ->as('kasir.')
        ->group(function () {
            Route::get('/', [KasirDashboardController::class, 'index'])->name('dashboard');

            // Penjualan
            Route::get('/penjualan', [KasirPenjualanController::class, 'dataPenjualan'])->name('penjualan.index');
            Route::get('/penjualan/create', [KasirPenjualanController::class, 'createPenjualan'])->name('penjualan.create');
            Route::post('/penjualan', [KasirPenjualanController::class, 'storePenjualan'])->name('penjualan.store');
            Route::get('/penjualan/{id}/struk', [KasirPenjualanController::class, 'strukPenjualan'])->name('penjualan.struk');
            Route::get('/penjualan/{id}', [KasirPenjualanController::class, 'detailPenjualan'])->name('penjualan.detail');
        });

    // Manager Route
    Route::get('/dashboard/manager', [managerController::class, 'index'])
        ->name('manager.dashboard')
        ->middleware(RoleMiddleware::class.':manager');

    // Gudang Route
    Route::get('/dashboard/gudang', [gudangController::class, 'index'])
        ->name('gudang.dashboard')
        ->middleware(RoleMiddleware::class.':petugas-gudang');

    // Penjualan Route
    Route::get('/dashboard/penjualan', [penjualanController::class, 'index'])
        ->name('penjualan.dashboard')
        ->middleware(RoleMiddleware::class.':staff-penjualan');
});
