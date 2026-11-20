<?php

namespace App\Livewire;

use App\Models\Kategori;
use App\Models\Resep;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

// Halaman Jelajah Resep.
// Cara kerja Livewire singkatnya:
// 1. Property public di bawah ini = "keadaan" halaman (kata kunci, filter, urutan).
// 2. Saat pengguna mengetik/klik, browser mengirim perubahan itu ke server.
// 3. Server menjalankan render() lagi, lalu HANYA bagian HTML yang berubah dikirim balik.
// Hasilnya: halaman berubah tanpa reload.
#[Title('Jelajah Resep')]
class Jelajah extends Component
{
    // #[Url] = nilainya ikut ditulis di alamat web, contoh: /jelajah?cari=ayam&kategori=sarapan
    // Jadi hasil pencarian bisa di-bookmark / dibagikan, dan tombol Back tetap berfungsi.
    // except: '' = kalau kosong, tidak usah ditulis di URL.
    #[Url(except: '')]
    public string $cari = '';

    #[Url(except: '')]
    public string $kategori = ''; // berisi slug, contoh: sarapan

    #[Url(except: '')]
    public string $kesulitan = ''; // Mudah / Sedang / Sulit

    #[Url(except: 'terbaru')]
    public string $urut = 'terbaru'; // terbaru / populer / rating / tercepat

    // Berapa resep yang ditampilkan. Bertambah 6 setiap kali digulir ke bawah.
    public int $jumlah = 6;

    // ===== Aksi (dipanggil dari tampilan dengan wire:click) =====

    // Klik kategori yang sama dua kali = batal memilih
    public function pilihKategori(string $slug): void
    {
        $this->kategori = $this->kategori === $slug ? '' : $slug;
    }

    public function pilihKesulitan(string $tingkat): void
    {
        $this->kesulitan = $this->kesulitan === $tingkat ? '' : $tingkat;
    }

    // Dipanggil saat pengguna menggulir sampai bawah (infinite scroll)
    public function muatLagi(): void
    {
        $this->jumlah += 6;
    }

    public function resetFilter(): void
    {
        // reset() = kembalikan property ke nilai awalnya
        $this->reset('cari', 'kategori', 'kesulitan', 'urut', 'jumlah');
    }

    // "Lifecycle hook": otomatis dijalankan Livewire setiap ada property yang berubah.
    // Kalau filternya berubah, mulai lagi dari 6 resep pertama.
    public function updated(string $properti): void
    {
        if ($properti !== 'jumlah') {
            $this->jumlah = 6;
        }
    }

    public function render()
    {
        // when(kondisi, fungsi) = tambahkan syarat HANYA kalau kondisinya terisi
        $query = Resep::query()
            ->when($this->cari, function ($q) {
                $kata = '%' . trim($this->cari) . '%';
                // Cari di judul, deskripsi, ATAU di daftar bahan.
                // Jadi mengetik "santan" akan menemukan semua resep yang memakai santan.
                $q->where(function ($q) use ($kata) {
                    $q->where('judul', 'like', $kata)
                        ->orWhere('deskripsi', 'like', $kata)
                        ->orWhereHas('bahan', fn ($b) => $b->where('isi', 'like', $kata));
                });
            })
            ->when($this->kategori, fn ($q) => $q->whereHas('kategori', fn ($k) => $k->where('slug', $this->kategori)))
            ->when($this->kesulitan, fn ($q) => $q->where('kesulitan', $this->kesulitan));

        // Hitung total SEBELUM dibatasi jumlahnya
        $total = (clone $query)->count();

        // Urutkan sesuai pilihan
        // untukKartu() dipasang di sini karena urutan "rating" butuh kolom komentar_avg_rating
        $query->untukKartu();

        match ($this->urut) {
            'populer' => $query->orderByDesc('dilihat'),
            'tercepat' => $query->orderBy('waktu'),
            'rating' => $query->orderByDesc('komentar_avg_rating')->orderByDesc('komentar_count'),
            default => $query->latest(),
        };

        $resep = $query->take($this->jumlah)->get();

        return view('livewire.jelajah', [
            'resep' => $resep,
            'total' => $total,
            'adaLagi' => $total > $resep->count(),
            'daftarKategori' => Kategori::withCount('resep')->get(),
            'adaFilter' => $this->cari || $this->kategori || $this->kesulitan || $this->urut !== 'terbaru',
        ]);
    }
}
