<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
| Halaman pemasangan untuk hosting yang tidak menyediakan terminal.
| Membuka /_pasang/{kode} akan menjalankan migrate + db:seed, lalu menampilkan hasilnya.
|
| Keamanan:
| - Hanya aktif kalau SETUP_TOKEN diisi, dan kodenya harus sama persis (hash_equals).
| - Tanpa kode yang benar → 404, seolah-olah halaman ini tidak ada.
| - Setelah selesai, HAPUS variabel SETUP_TOKEN di hosting.
|
| File ini dimuat di luar grup "web" (lihat bootstrap/app.php), jadi tidak butuh tabel
| sesi di database. Penting, karena tabel-tabel itu justru belum ada saat pertama kali.
*/

Route::get('/_pasang/{kode}', function (string $kode) {
    $rahasia = config('app.setup_token');
    abort_unless(filled($rahasia) && hash_equals($rahasia, $kode), 404);

    $hasil = '';
    try {
        Artisan::call('migrate', ['--force' => true]);
        $hasil .= Artisan::output();

        Artisan::call('db:seed', ['--force' => true]);
        $hasil .= Artisan::output();

        $hasil .= "\nSELESAI. Jangan lupa hapus variabel SETUP_TOKEN di hosting.";
    } catch (Throwable $e) {
        // Tampilkan penyebabnya (hanya terlihat oleh yang tahu kode rahasia)
        $hasil .= "\nGAGAL: " . get_class($e) . "\n" . $e->getMessage();
    }

    return response($hasil)->header('Content-Type', 'text/plain; charset=utf-8');
});
