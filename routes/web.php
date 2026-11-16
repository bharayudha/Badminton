<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PairingSessionController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])->name('register.store');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('role:admin')->group(function (): void {
        Route::resource('pemain', PlayerController::class)
            ->parameters(['pemain' => 'player'])
            ->names('players');

        Route::get('/pengaturan', [PairingSessionController::class, 'create'])->name('pairing.create');
        Route::post('/pengaturan', [PairingSessionController::class, 'store'])->name('pairing.store');
        Route::get('/riwayat-pengaturan', [PairingSessionController::class, 'index'])->name('pairing.index');
        Route::get('/riwayat-pengaturan/{pairingSession}', [PairingSessionController::class, 'show'])->name('pairing.show');

        Route::get('/pengguna', [UserManagementController::class, 'index'])->name('users.index');
        Route::put('/pengguna/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/pengguna/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    });
});
