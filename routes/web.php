<?php

use Illuminate\Support\Facades\Route;
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
Route::get('/', function () {
    $layanans = Layanan::latest()->get();

    return view('welcome', compact('layanans'));
})->name('home');

Route::get('/quote-request', function () {
    return view('quote.request');
})->name('quote.request');

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

    Route::get('/dashboard', function () {
        // duplicate of home; keep data for direct URL
        $totalUser = \App\Models\User::count();
        $totalOrder = \App\Models\Transaksi::count();
        $totalSales = \App\Models\Transaksi::sum('total_harga');
        $orderPending = \App\Models\Transaksi::where('sisa_bayar', '>', 0)->sum('sisa_bayar');

        $endDate = Carbon::today();
        $startDate = $endDate->copy()->subDays(6);

        $dailyRanges = collect(range(0, 6))->map(function ($offset) use ($startDate) {
            return $startDate->copy()->addDays($offset);
        });

        $dailyLabels = $dailyRanges->map(function ($date) {
            return $date->format('d M');
        })->toArray();

        $dailyTotals = \App\Models\Transaksi::whereBetween('tanggal_order', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->selectRaw('DATE(tanggal_order) as order_date, SUM(total_harga) as total_sales, SUM(sisa_bayar) as total_piutang')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->get()
            ->keyBy('order_date');

        $dailyRevenueData = $dailyRanges->map(function ($date) use ($dailyTotals) {
            return $dailyTotals->get($date->toDateString())->total_sales ?? 0;
        })->toArray();

        $dailyDebtData = $dailyRanges->map(function ($date) use ($dailyTotals) {
            return $dailyTotals->get($date->toDateString())->total_piutang ?? 0;
        })->toArray();

        return view('shared.dashboard', compact('totalUser', 'totalOrder', 'totalSales', 'orderPending', 'dailyLabels', 'dailyRevenueData', 'dailyDebtData'));
    })->name('dashboard');

    Route::resource('users', UserController::class)->middleware('role:admin');

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
