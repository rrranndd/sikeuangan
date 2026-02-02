<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AuthCustom;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return session()->has('user_id')
        ? redirect('/dashboard')
        : redirect('/login');
});

Route::get('/cek-db', function () {
    return DB::connection()->getPdo()
        ? '✅ PostgreSQL CONNECTED'
        : '❌ FAILED';
});



Route::get('/db-test', function () {
    return DB::connection()->getDatabaseName();
});


Route::get('/login', [AuthController::class, 'loginPage'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'registerPage']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([AuthCustom::class])->group(function () {

    Route::get('/dashboard', [TransaksiController::class, 'index']);
    Route::get('/dashboard/ajax', [TransaksiController::class, 'ajaxDashboardHarian']);

    Route::get('/transaksi', [TransaksiController::class, 'transaksiPage']);
    Route::get('/transaksi/ajax', [TransaksiController::class, 'ajaxTransaksi']);
    Route::post('/transaksi/store', [TransaksiController::class, 'store']);
    Route::post('/transaksi/update', [TransaksiController::class, 'update']);
    Route::post('/transaksi/delete', [TransaksiController::class, 'delete']);

    Route::get('/laporan', [LaporanController::class, 'index']);
    Route::get('/laporan/ajax', [LaporanController::class, 'ajax']);
    Route::get('/laporan/export', [LaporanController::class, 'exportExcel']);

    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile/update', [AuthController::class, 'updateProfile']);

});
