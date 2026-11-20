<?php

namespace App\Livewire;

use App\Models\Resep;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

// Bagian komentar & rating di halaman detail resep.
// Dipasang di Blade dengan: <livewire:komentar-resep :resep="$resep" />
class KomentarResep extends Component
{
    // Resep yang sedang dibuka (dikirim dari halaman detail)
    public Resep $resep;

    // Isian form, terhubung ke input lewat wire:model
    public string $nama = '';
    public int $rating = 0;
    public string $isi = '';

    // Aturan validasi (sama seperti $request->validate() di controller)
    protected function rules(): array
    {
        return [
            'nama' => 'required|string|min:2|max:50',
            'rating' => 'required|integer|between:1,5',
            'isi' => 'required|string|min:5|max:1000',
        ];
    }

    protected function messages(): array
    {
        return ['rating.between' => 'Pilih bintang 1 sampai 5 dulu.'];
    }

    protected function validationAttributes(): array
    {
        return ['isi' => 'komentar'];
    }

    // Dipanggil saat form dikirim (wire:submit="kirim")
    public function kirim(): void
    {
        $this->validate();

        // Cegah spam: maksimal 3 komentar per menit dari alamat IP yang sama
        $kunci = 'komentar:' . request()->ip();
        if (RateLimiter::tooManyAttempts($kunci, 3)) {
            $this->addError('isi', 'Terlalu banyak komentar. Coba lagi dalam ' . RateLimiter::availableIn($kunci) . ' detik.');

            return;
        }
        RateLimiter::hit($kunci, 60);

        $this->resep->komentar()->create([
            'nama' => trim($this->nama),
            'rating' => $this->rating,
            'isi' => trim($this->isi),
        ]);

        // Kosongkan rating & isi. Nama dibiarkan, supaya tidak perlu diketik ulang.
        $this->reset('rating', 'isi');

        // Kirim "acara" ke browser → Alpine menampilkan pesan terima kasih & mereset bintang
        $this->dispatch('komentar-terkirim');
    }

    // Hapus komentar: hanya admin
    public function hapus(int $id): void
    {
        Gate::authorize('admin');
        $this->resep->komentar()->whereKey($id)->delete();
    }

    public function render()
    {
        $daftar = $this->resep->komentar()->get();

        // Berapa komentar untuk tiap jumlah bintang, contoh: [5 => 3, 4 => 1, 3 => 0, ...]
        $sebaran = collect([5, 4, 3, 2, 1])
            ->mapWithKeys(fn ($b) => [$b => $daftar->where('rating', $b)->count()]);

        return view('livewire.komentar-resep', [
            'daftar' => $daftar,
            'rataRata' => $daftar->avg('rating'),
            'sebaran' => $sebaran,
        ]);
    }
}
