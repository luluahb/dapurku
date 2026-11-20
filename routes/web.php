<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\HalamanController;
use App\Http\Controllers\ResepController;
use App\Livewire\Jelajah;
use Illuminate\Support\Facades\Route;

// ================= HALAMAN PENGUNJUNG (tanpa login) =================

// Data Beranda diambil dari database lewat HalamanController
Route::get('/', [HalamanController::class, 'beranda'])->name('beranda');

// Detail resep. {resep} dicari lewat SLUG (lihat getRouteKeyName di model Resep),
// contoh: /resep/rendang-daging-sapi-empuk-bumbu-meresap
Route::get('/resep/{resep}', [ResepController::class, 'show'])->name('resep.show');

// Halaman Jelajah: langsung memakai komponen Livewire sebagai satu halaman penuh (tanpa controller)
Route::livewire('/jelajah', Jelajah::class)->name('jelajah');

// ================= ADMIN =================

// Form login admin: hanya untuk yang BELUM login (middleware guest)
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [Admin\AuthController::class, 'create'])->name('admin.login');
    // throttle:5,1 = maksimal 5 percobaan login per menit (mencegah tebak-tebakan password)
    Route::post('/admin/login', [Admin\AuthController::class, 'store'])->middleware('throttle:5,1');
});

// Halaman admin: harus login (auth) DAN admin (can:admin)
// prefix('admin')  → semua URL diawali /admin
// name('admin.')   → semua nama route diawali admin.  (contoh: admin.resep.index)
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/resep'); // /admin → langsung ke daftar resep
    Route::resource('resep', Admin\ResepController::class)->except('show');
    Route::post('/logout', [Admin\AuthController::class, 'destroy'])->name('logout');
});
