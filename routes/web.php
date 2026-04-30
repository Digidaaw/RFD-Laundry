<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NewsletterController;
use App\Models\Layanan;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// public welcome page shown on first run
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/quote-request', [PageController::class, 'quoteRequest'])->name('quote.request');

Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
    ->name('newsletter.subscribe');

// --- ROUTE UNTUK PUBLIK / LOGIN & REGISTER ---
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate']);

    // Route untuk Registrasi
    Route::get('/register', [RegisterController::class, 'index'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});


// --- ROUTE YANG MEMERLUKAN LOGIN ---
Route::middleware(['auth', 'role'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->middleware('role:admin');

    Route::resource('layanan', LayananController::class);

    Route::resource('pelanggan', PelangganController::class)->except(['destroy']);

    Route::resource('transaksi', TransaksiController::class)->except(['destroy']);
    Route::patch('/transaksi/{transaksi}/bayar', [TransaksiController::class, 'bayarPiutang'])->name('transaksi.bayar');

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/periode', [ReportController::class, 'laporanPeriode'])->name('report.periode');
    Route::get('/report/piutang', [ReportController::class, 'laporanPiutang'])->name('report.piutang');

    Route::get('/report/pelanggan/{pelanggan}', [ReportController::class, 'laporanPelanggan'])->name('report.pelanggan');
    Route::get('/report/pelanggan/{pelanggan}/pdf', [ReportController::class, 'exportPdfPelanggan'])->name('report.pelanggan.pdf');
    Route::get('/report/pelanggan/{pelanggan}/excel', [ReportController::class, 'exportPelangganXls'])->name('report.pelanggan.excel');



});
