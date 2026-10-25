<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login', 301);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
