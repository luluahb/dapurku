<?php

namespace Database\Seeders;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // Catatan: "use WithoutModelEvents" bawaan Laravel sengaja dihapus,
    // karena itu mematikan booted()/creating di model Resep (pembuat slug otomatis).

    public function run(): void
    {
        // ===== Akun admin (satu-satunya akun yang bisa login) =====
        // Password diambil dari .env (ADMIN_PASSWORD). Kalau tidak ada, pakai "password123".
        // PENTING: sebelum website online, isi ADMIN_PASSWORD dengan password yang kuat.
        // firstOrNew: kalau admin dengan email ini sudah ada, pakai yang lama (tidak dibuat dobel).
        $admin = User::firstOrNew(['email' => env('ADMIN_EMAIL', 'admin@dapurku.test')]);
        if (! $admin->exists) {
            $admin->name = 'Admin Dapurku';
            $admin->password = env('ADMIN_PASSWORD', 'password123');
        }
        // forceFill: is_admin sengaja TIDAK dimasukkan ke $fillable,
        // supaya tidak ada orang yang bisa menjadikan dirinya admin lewat form.
        $admin->forceFill(['is_admin' => true])->save();

        // Data contoh hanya diisi SEKALI. Kalau kategori sudah ada, berarti sudah pernah di-seed.
        // Jadi seeder ini aman dijalankan berkali-kali (misalnya setiap deploy).
        if (Kategori::exists()) {
            $this->command?->info('Data contoh sudah ada, dilewati.');

            return;
        }

        // Urutan penting: kategori dulu, baru resep (resep butuh kategori)
        $this->call([
            KategoriSeeder::class,
            ResepSeeder::class,
        ]);
    }
}
