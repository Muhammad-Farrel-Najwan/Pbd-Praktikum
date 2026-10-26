<?php

use App\Http\Controllers\adminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\gudangController;
use App\Http\Controllers\kasirController;
use App\Http\Controllers\managerController;
use App\Http\Controllers\penjualanController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login', 301);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',[AuthController::class, 'login'])->name('login.process');

Route::get('/dashboard/admin',[adminController::class, 'index'])->name('admin.dashboard');
Route::get('/dashboard/manager',[managerController::class, 'index'])->name('manager.dashboard');
Route::get('/dashboard/kasir',[kasirController::class, 'index'])->name('kasir.dashboard');
Route::get('/dashboard/gudang',[gudangController::class, 'index'])->name('gudang.dashboard');
Route::get('/dashboard/penjualan',[penjualanController::class, 'index'])->name('penjualan.dashboard');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
