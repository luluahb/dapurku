<?php

namespace Database\Seeders;

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
        User::create([
            'name' => 'Admin Dapurku',
            'email' => env('ADMIN_EMAIL', 'admin@dapurku.test'),
            'password' => env('ADMIN_PASSWORD', 'password123'),
        ])->forceFill(['is_admin' => true])->save();
        // forceFill: is_admin sengaja TIDAK dimasukkan ke $fillable,
        // supaya tidak ada orang yang bisa menjadikan dirinya admin lewat form.

        // Urutan penting: kategori dulu, baru resep (resep butuh kategori)
        $this->call([
            KategoriSeeder::class,
            ResepSeeder::class,
        ]);
    }
}
