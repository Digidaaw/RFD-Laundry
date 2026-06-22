<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\NewsletterController;

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
});


// --- ROUTE YANG MEMERLUKAN LOGIN ---
Route::middleware(['auth', 'role'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->only(['index', 'store', 'update'])->middleware('role:admin');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('role:admin');

    Route::resource('layanan', LayananController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/layanan/{layanan}/toggle-status', [LayananController::class, 'toggleStatus'])->name('layanan.toggle-status');

    Route::resource('pelanggan', PelangganController::class)->only(['index', 'store', 'update']);

    Route::resource('transaksi', TransaksiController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/transaksi/{transaksi}/bayar', [TransaksiController::class, 'bayarPiutang'])->name('transaksi.bayar');
    Route::get('/transaksi/{transaksi}/cetak-struk', [TransaksiController::class, 'cetakStruk'])->name('transaksi.cetak-struk');

    Route::get('/report', [ReportController::class, 'index'])->name('report.index');
    Route::get('/report/periode', [ReportController::class, 'laporanPeriode'])->name('report.periode');
    Route::get('/report/piutang', [ReportController::class, 'laporanPiutang'])->name('report.piutang');

    Route::get('/report/pelanggan/{pelanggan}', [ReportController::class, 'laporanPelanggan'])->name('report.pelanggan');
    Route::get('/report/pelanggan/{pelanggan}/pdf', [ReportController::class, 'exportPdfPelanggan'])->name('report.pelanggan.pdf');
    Route::get('/report/pelanggan/{pelanggan}/excel', [ReportController::class, 'exportPelangganXls'])->name('report.pelanggan.excel');



});
