<?php

use App\Http\Controllers\ApiController;
use App\Http\Controllers\KecamatansController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PointsController;
use App\Http\Controllers\PolygonsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TrafosController;
use App\Http\Controllers\StatistikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rute Publik (Tanpa Login)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/map', function () {
    return view('map');
});

Route::get('/statistik', [StatistikController::class, 'index'])->name('statistik');

/*
|--------------------------------------------------------------------------
| Layanan API Data Spasial GeoJSON (Akses Publik)
|--------------------------------------------------------------------------
*/

Route::get('/api/kecamatans', [ApiController::class, 'kecamatans'])->name('api.kecamatans');
Route::get('/api/trafos', [ApiController::class, 'trafos'])->name('api.trafos');
Route::get('/api/points', [ApiController::class, 'points'])->name('api.points');
Route::get('/api/polygons', [ApiController::class, 'polygons'])->name('api.polygons');
Route::get('/api/laporans', [ApiController::class, 'laporans'])->name('api.laporans');


/*
|--------------------------------------------------------------------------
| Rute Pengguna Umum Terautentikasi (Wajib Login)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/laporan/store', [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| Rute Khusus Manajemen Admin / Petugas (Wajib Login & Peran Admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'IsAdmin'])->group(function () {
    // Pembaruan Status Kelistrikan Batas Administrasi Kecamatan
    Route::patch('/kecamatan/update/{id}', [KecamatansController::class, 'update'])->name('kecamatan.update');

    // Pembaruan Status Tindakan atau Penanganan Laporan Gangguan Warga
    Route::patch('/laporan/update/{id}', [LaporanController::class, 'update'])->name('laporan.update');

    // Manajemen Penyimpanan Aset Titik Trafo PLN
    Route::post('/trafo/store', [TrafosController::class, 'store'])->name('trafo.store');
    Route::patch('/trafo/update/{id}', [TrafosController::class, 'update'])->name('trafo.update');

    // Manajemen Poligon Area Pemadaman Manual (Arsip / Cadangan)
    Route::post('/polygon/store', [PolygonsController::class, 'store'])->name('polygon.store');
    Route::patch('/polygon/update/{id}', [PolygonsController::class, 'update'])->name('polygon.update');
    Route::delete('/polygon/delete/{id}', [PolygonsController::class, 'destroy'])->name('polygon.destroy');

    // Manajemen Titik Koordinat Gardu Lama (Arsip / Cadangan)
    Route::post('/point/store', [PointsController::class, 'store'])->name('point.store');
    Route::patch('/point/update/{id}', [PointsController::class, 'update'])->name('point.update');
    Route::delete('/point/delete/{id}', [PointsController::class, 'destroy'])->name('point.destroy');

});

/*
|--------------------------------------------------------------------------
| Otentikasi Sistem (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';
