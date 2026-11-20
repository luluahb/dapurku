<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Resep;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

// CRUD resep untuk admin. Semua route-nya dilindungi middleware 'auth' + 'can:admin'.
class ResepController extends Controller
{
    // GET /admin/resep → daftar semua resep
    public function index(Request $request)
    {
        $cari = $request->query('cari');

        $resep = Resep::with('kategori')
            ->when($cari, fn ($q) => $q->where('judul', 'like', "%{$cari}%"))
            ->latest()
            ->paginate(10)        // 10 per halaman + tombol halaman otomatis
            ->withQueryString();  // ?cari=... tetap terbawa saat pindah halaman

        $statistik = [
            'resep' => Resep::count(),
            'dilihat' => Resep::sum('dilihat'),
            'kategori' => Kategori::count(),
        ];

        return view('admin.resep.index', compact('resep', 'statistik', 'cari'));
    }

    // GET /admin/resep/create → form tambah
    public function create()
    {
        return view('admin.resep.form', [
            'resep' => new Resep(['porsi' => 2, 'kesulitan' => 'Mudah']), // resep kosong
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // POST /admin/resep → simpan resep baru
    public function store(Request $request)
    {
        $data = $this->validasi($request);

        // DB::transaction = semua berhasil, atau semua dibatalkan.
        // Jadi tidak mungkin ada resep tersimpan tanpa bahan karena error di tengah jalan.
        $resep = DB::transaction(function () use ($request, $data) {
            $resep = new Resep(Arr::except($data, 'foto')); // foto diurus terpisah di bawah
            $resep->user_id = $request->user()->id;
            $resep->foto = $this->simpanFoto($request);
            $resep->save();

            $this->simpanBahanLangkah($resep, $data);

            return $resep;
        });

        return redirect()->route('admin.resep.index')
            ->with('pesan', "Resep \"{$resep->judul}\" berhasil ditambahkan. 🎉");
    }

    // GET /admin/resep/{resep}/edit → form edit (isinya sudah terisi)
    public function edit(Resep $resep)
    {
        $resep->load('bahan', 'langkah');

        return view('admin.resep.form', [
            'resep' => $resep,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // PUT /admin/resep/{resep} → simpan perubahan
    public function update(Request $request, Resep $resep)
    {
        $data = $this->validasi($request);

        DB::transaction(function () use ($request, $resep, $data) {
            $resep->fill(Arr::except($data, 'foto'));

            // Ganti foto HANYA kalau admin memilih foto baru
            if ($request->hasFile('foto')) {
                $this->hapusFoto($resep);
                $resep->foto = $this->simpanFoto($request);
            }
            $resep->save();

            // Cara paling sederhana memperbarui bahan & langkah: hapus semua, lalu buat ulang
            $resep->bahan()->delete();
            $resep->langkah()->delete();
            $this->simpanBahanLangkah($resep, $data);
        });

        return redirect()->route('admin.resep.index')
            ->with('pesan', "Resep \"{$resep->judul}\" berhasil diperbarui. ✅");
    }

    // DELETE /admin/resep/{resep} → hapus
    public function destroy(Resep $resep)
    {
        $this->hapusFoto($resep);
        $resep->delete(); // bahan & langkah ikut terhapus (cascadeOnDelete di migration)

        return redirect()->route('admin.resep.index')
            ->with('pesan', "Resep \"{$resep->judul}\" sudah dihapus. 🗑️");
    }

    // ================= Fungsi bantu (private = hanya dipakai di controller ini) =================

    private function validasi(Request $request): array
    {
        // Buang baris bahan/langkah yang kosong SEBELUM divalidasi,
        // jadi kalau admin menambah baris lalu tidak diisi, tidak dianggap error.
        $request->merge([
            'bahan' => $this->buangKosong($request->input('bahan', [])),
            'langkah' => $this->buangKosong($request->input('langkah', [])),
        ]);

        return $request->validate([
            'judul' => 'required|string|min:5|max:120',
            'kategori_id' => 'required|exists:kategori,id',
            'deskripsi' => 'required|string|min:20|max:1000',
            'waktu' => 'required|integer|min:1|max:1440',
            'porsi' => 'required|integer|min:1|max:50',
            'kesulitan' => 'required|in:Mudah,Sedang,Sulit',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048', // maks 2 MB
            'bahan' => 'required|array|min:1',
            'bahan.*' => 'string|max:255',
            'langkah' => 'required|array|min:1',
            'langkah.*' => 'string|max:2000',
        ], [
            'bahan.required' => 'Isi minimal satu bahan.',
            'langkah.required' => 'Isi minimal satu langkah.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ], [
            'kategori_id' => 'kategori',
            'waktu' => 'waktu memasak',
        ]);
    }

    private function buangKosong(array $isi): array
    {
        return array_values(array_filter(array_map('trim', $isi), fn ($baris) => $baris !== ''));
    }

    private function simpanBahanLangkah(Resep $resep, array $data): void
    {
        foreach ($data['bahan'] as $n => $isi) {
            $resep->bahan()->create(['urutan' => $n + 1, 'isi' => $isi]);
        }
        foreach ($data['langkah'] as $n => $isi) {
            $resep->langkah()->create(['urutan' => $n + 1, 'isi' => $isi]);
        }
    }

    // Simpan foto ke storage/app/public/resep/namaacak.jpg → hasilnya "resep/namaacak.jpg"
    private function simpanFoto(Request $request): ?string
    {
        return $request->hasFile('foto')
            ? $request->file('foto')->store('resep', 'public')
            : null;
    }

    // Hapus file foto lama, KECUALI foto contoh yang berupa link https://
    private function hapusFoto(Resep $resep): void
    {
        if ($resep->foto && ! Str::startsWith($resep->foto, 'http')) {
            Storage::disk('public')->delete($resep->foto);
        }
    }
}
