<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- ROUTE UNTUK PUBLIK / LOGIN & REGISTER ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    // Route untuk Registrasi
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});


// --- ROUTE YANG MEMERLUKAN LOGIN ---
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return view('shared.dashboard');
    })->name('home');
    Route::get('/dashboard', function () {
        return view('shared.dashboard');
    })->name('dashboard');

    Route::resource('users', UserController::class);

    Route::resource('layanan', LayananController::class);

    Route::resource('pelanggan', PelangganController::class);

    Route::resource('transaksi', TransaksiController::class);
    Route::patch('/transaksi/{transaksi}/bayar', [TransaksiController::class, 'bayarPiutang'])->name('transaksi.bayar');

    Route::resource('kasir', UserController::class);

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/periode', [ReportController::class, 'laporanPeriode'])->name('report.periode');
    Route::get('/report/piutang', [ReportController::class, 'laporanPiutang'])->name('report.piutang');
});
