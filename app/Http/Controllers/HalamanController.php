<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Resep;
use App\Models\User;

class HalamanController extends Controller
{
    public function beranda()
    {
        // Semua kategori + jumlah resepnya (hasilnya ada di $k->resep_count)
        $kategori = Kategori::withCount('resep')->get();

        // with('user', 'kategori') = eager loading: ambil penulis & kategori sekaligus,
        // supaya tidak ada query tambahan untuk tiap kartu resep
        $resepPopuler = Resep::with('user', 'kategori')
            ->orderByDesc('dilihat') // paling sering dibuka di atas
            ->take(3)
            ->get();

        $resepTerbaru = Resep::with('user', 'kategori')
            ->latest() // created_at paling baru di atas
            ->take(4)
            ->get();

        // Angka-angka di bagian hero
        $statistik = [
            'resep' => Resep::count(),
            'penulis' => User::has('resep')->count(), // hanya user yang punya resep
            'kategori' => $kategori->count(),
        ];

        return view('beranda', compact('kategori', 'resepPopuler', 'resepTerbaru', 'statistik'));
    }
}
