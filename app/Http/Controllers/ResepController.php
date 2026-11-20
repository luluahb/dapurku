<?php

namespace App\Http\Controllers;

use App\Models\Resep;

class ResepController extends Controller
{
    // Route model binding: Laravel otomatis mencari resep berdasarkan slug di URL.
    // Kalau slug-nya tidak ada di database → otomatis halaman 404.
    public function show(Resep $resep)
    {
        // ===== Hitung "dilihat" =====
        // Supaya refresh berkali-kali tidak menambah angka terus,
        // tiap pengunjung hanya dihitung SEKALI per sesi (dicatat di session).
        $kunci = 'sudah_dilihat.' . $resep->id;
        if (! session()->has($kunci)) {
            $resep->increment('dilihat'); // dilihat = dilihat + 1 langsung di database
            session()->put($kunci, true);
        }

        // load() = eager loading untuk model yang SUDAH diambil (kebalikan with() yang dipakai saat mengambil)
        $resep->load('user', 'kategori', 'bahan', 'langkah');

        // ===== Resep lainnya =====
        // Ambil 3 resep selain resep ini. Yang kategorinya sama diletakkan paling atas,
        // kalau kurang dari 3, sisanya diisi resep terbaru dari kategori lain.
        $resepLain = Resep::with('user', 'kategori')
            ->whereKeyNot($resep->id)                                    // bukan resep ini
            ->orderByRaw('kategori_id = ? DESC', [$resep->kategori_id]) // kategori sama di atas
            ->latest()
            ->take(3)
            ->get();

        return view('resep.show', compact('resep', 'resepLain'));
    }
}
