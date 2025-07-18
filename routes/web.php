<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LayananController;
use App\Http\Controllers\PelangganController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|

*/
Route::resource('users', UserController::class)->middleware('auth');

Route::resource('users', UserController::class);

// Route::get('/kasir', [UserController::class, 'index'])->name('kasir.index');
// Route::post('/kasir', [UserController::class, 'store'])->name('users.store');
// Route::post('/kasir', [UserController::class, 'update'])->name('users.edit');
// Route::post('/kasir', [UserController::class, 'destroy'])->name('users.destroy');



// Route::middleware(['auth'])->group(function () {
Route::resource('users', UserController::class); 
Route::resource('layanan', LayananController::class);
Route::resource('pelanggan', PelangganController::class);

// });

Route::get('/', function () {
    return view('shared.dashboard');
});
Route::get('/dashboard', function () {
    return view('shared.dashboard');
});




Route::get('/kasir', [UserController::class, 'index'])->name('kasir.index');

