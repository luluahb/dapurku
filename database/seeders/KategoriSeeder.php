<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['nama' => 'Makanan Utama', 'ikon' => '🍛'],
            ['nama' => 'Sarapan', 'ikon' => '🍳'],
            ['nama' => 'Mi & Pasta', 'ikon' => '🍜'],
            ['nama' => 'Sup & Soto', 'ikon' => '🍲'],
            ['nama' => 'Camilan', 'ikon' => '🥟'],
            ['nama' => 'Kue & Dessert', 'ikon' => '🍰'],
            ['nama' => 'Minuman', 'ikon' => '🥤'],
            ['nama' => 'Sayuran', 'ikon' => '🥗'],
        ];

        foreach ($daftar as $item) {
            Kategori::create([
                'nama' => $item['nama'],
                'slug' => Str::slug($item['nama']), // "Mi & Pasta" → "mi-pasta"
                'ikon' => $item['ikon'],
            ]);
        }
    }
}
