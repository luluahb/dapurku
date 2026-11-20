<?php

namespace App\Livewire;

use App\Models\Resep;
use Livewire\Attributes\Title;
use Livewire\Component;

// Halaman "Favoritku": resep yang disimpan pengunjung.
// Daftar id resepnya ada di localStorage BROWSER, jadi server tidak tahu isinya.
// Caranya: begitu halaman terbuka, Alpine membaca localStorage lalu mengirim id-nya ke sini.
#[Title('Favoritku')]
class Favoritku extends Component
{
    public array $ids = [];       // id resep yang disimpan, dikirim dari browser
    public bool $sudahDimuat = false; // false = masih menunggu data dari browser

    // Dipanggil dari browser: $wire.muat([3, 7, 1])
    public function muat(array $ids): void
    {
        // Pastikan isinya hanya angka (data dari browser tidak boleh langsung dipercaya)
        $this->ids = array_values(array_filter($ids, 'is_int'));
        $this->sudahDimuat = true;
    }

    public function render()
    {
        $resep = Resep::untukKartu()->whereIn('id', $this->ids)->get()
            // Urutkan sesuai urutan di browser (yang terakhir disimpan di depan)
            ->sortBy(fn ($r) => array_search($r->id, $this->ids))
            ->values();

        return view('livewire.favoritku', compact('resep'));
    }
}
