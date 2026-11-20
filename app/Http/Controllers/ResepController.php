<?php

namespace App\Http\Controllers;

use App\Models\Resep;
use Illuminate\Http\Request;

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
        $resep->load('user', 'kategori', 'bahan', 'langkah')
            ->loadCount('komentar')            // → $resep->komentar_count
            ->loadAvg('komentar', 'rating');   // → $resep->komentar_avg_rating

        // ===== Resep lainnya =====
        // Ambil 3 resep selain resep ini. Yang kategorinya sama diletakkan paling atas,
        // kalau kurang dari 3, sisanya diisi resep terbaru dari kategori lain.
        $resepLain = Resep::untukKartu()
            ->whereKeyNot($resep->id)                                    // bukan resep ini
            ->orderByRaw('kategori_id = ? DESC', [$resep->kategori_id]) // kategori sama di atas
            ->latest()
            ->take(3)
            ->get();

        return view('resep.show', compact('resep', 'resepLain'));
    }

    // POST /resep/{resep}/suka  → dipanggil JavaScript (fetch), bukan dari form.
    // Jawabannya JSON, bukan halaman HTML.
    public function suka(Request $request, Resep $resep)
    {
        $request->validate(['aksi' => 'required|in:tambah,kurang']);

        // Session mencatat resep apa saja yang sudah disukai pengunjung ini,
        // supaya satu orang tidak bisa menambah suka berkali-kali untuk resep yang sama.
        $disukai = session('disukai', []);
        $sudah = in_array($resep->id, $disukai);

        if ($request->aksi === 'tambah' && ! $sudah) {
            $resep->increment('suka');
            session()->put('disukai', [...$disukai, $resep->id]);
        }

        if ($request->aksi === 'kurang' && $sudah && $resep->suka > 0) {
            $resep->decrement('suka');
            session()->put('disukai', array_values(array_diff($disukai, [$resep->id])));
        }

        return response()->json(['suka' => $resep->suka]);
    }
}
