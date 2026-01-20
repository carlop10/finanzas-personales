<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\BilleteraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BilleteraDistribucionController;
use App\Http\Controllers\BilleteraTransferController;
use App\Http\Controllers\UserProfileController;

// Rutas públicas (sin autenticación)
Route::get('login', [AuthController::class, 'showLogin'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.store');
Route::get('register', [AuthController::class, 'showRegister'])->name('register');
Route::post('register', [AuthController::class, 'register'])->name('register.store');

// Rutas protegidas (con autenticación)
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('perfil', [UserProfileController::class, 'show'])->name('profile.show');
    Route::put('perfil', [UserProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('perfil/password', [UserProfileController::class, 'updatePassword'])->name('profile.password');

    Route::get('billeteras/transferencias', [BilleteraTransferController::class, 'index'])
        ->name('billeteras.transferencias.index');
    Route::post('billeteras/transferencias', [BilleteraTransferController::class, 'store'])
        ->name('billeteras.transferencias.store');

    Route::post('billeteras/{billetera}/distribuciones', [BilleteraDistribucionController::class, 'store'])
        ->name('billeteras.distribuciones.store');
    Route::put('billeteras/{billetera}/distribuciones/{distribucion}', [BilleteraDistribucionController::class, 'update'])
        ->name('billeteras.distribuciones.update');
    Route::delete('billeteras/{billetera}/distribuciones/{distribucion}', [BilleteraDistribucionController::class, 'destroy'])
        ->name('billeteras.distribuciones.destroy');

    Route::get('movimientos/export', [MovimientoController::class, 'export'])->name('movimientos.export');
    Route::resource('movimientos', MovimientoController::class)->except(['show']);

    Route::resource('billeteras', BilleteraController::class);
});
