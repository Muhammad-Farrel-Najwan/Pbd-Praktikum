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

Route::redirect('/', '/login', 301);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');

//admin
Route::get('/dashboard/admin', [adminController::class, 'index'])->name('admin.dashboard')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':admin');
Route::post('/dashboard/admin/margin', [adminController::class, 'updateMargin'])->name('admin.dashboard.margin.update')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':admin');
Route::get('/dashboard/admin/barang', [adminController::class, 'dataBarang'])->name('admin.dashboard.barang')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':admin');
Route::get('/dashboard/admin/barang/tambah', [adminController::class, 'createBarang'])->name('admin.barang.create')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':admin');
Route::post('/dashboard/admin/barang', [adminController::class, 'storeBarang'])->name('admin.barang.store')
->middleware(AuthMiddleware::class, RoleMiddleware::class. ':admin');


//manager
Route::get('/dashboard/manager', [managerController::class, 'index'])->name('manager.dashboard')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':manager');

Route::get('/dashboard/kasir', [kasirController::class, 'index'])->name('kasir.dashboard')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':kasir');

Route::get('/dashboard/gudang', [gudangController::class, 'index'])->name('gudang.dashboard')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':gudang');

Route::get('/dashboard/penjualan', [penjualanController::class, 'index'])->name('penjualan.dashboard')
    ->middleware(AuthMiddleware::class, RoleMiddleware::class . ':penjualan');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
