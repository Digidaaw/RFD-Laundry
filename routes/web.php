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

// public welcome page shown on first run
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('welcome');

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

    Route::get('/dashboard', function () {
        // duplicate of home; keep data for direct URL
        $totalUser = \App\Models\User::count();
        $totalOrder = \App\Models\Transaksi::count();
        $totalSales = \App\Models\Transaksi::sum('jumlah_bayar');
        $orderPending = \App\Models\Transaksi::where('sisa_bayar', '>', 0)->sum('sisa_bayar');

        return view('shared.dashboard', compact('totalUser', 'totalOrder', 'totalSales', 'orderPending'));
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

    Route::get('/report/pelanggan/{pelanggan}', [ReportController::class, 'laporanPelanggan'])->name('report.pelanggan');

      Route::get('/report/pelanggan/{pelanggan}/pdf', [ReportController::class, 'exportPdfPelanggan'])->name('report.pelanggan.pdf');
      Route::get('/report/pelanggan/excel/{id}', [TransaksiController::class, 'exportPelangganExcel'])
    ->name('report.pelanggan.excel');



});
