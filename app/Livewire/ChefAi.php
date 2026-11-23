<?php

namespace App\Livewire;

use App\Models\Resep;
use App\Services\ChefAi as OtakChefAi;
use App\Services\GeminiGagal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

// Widget Chef AI: tombol chat di pojok kanan bawah, ada di setiap halaman (dipasang di layout).
//
// Alurnya dibuat DUA langkah supaya terasa cepat:
//   1. tanya()  → pertanyaan langsung tampil di layar + muncul animasi "Chef AI sedang mengetik"
//   2. jawab()  → dipanggil otomatis sesudahnya, di sinilah Gemini dihubungi (butuh beberapa detik)
//
// Obrolan disimpan di SESSION, jadi tidak hilang saat pengunjung pindah halaman
// (misalnya setelah mengklik resep yang disarankan Chef AI).
class ChefAi extends Component
{
    private const KUNCI_SESSION = 'chef-ai.percakapan';

    // Maksimal pesan yang disimpan di session (supaya session tidak membengkak)
    private const MAKS_PESAN = 20;

    // Isi kotak ketik
    public string $pertanyaan = '';

    // Riwayat obrolan: [['peran' => 'user'|'chef', 'teks' => '...', 'resep_id' => [..]], ...]
    public array $percakapan = [];

    // true selama menunggu jawaban Gemini
    public bool $menunggu = false;

    // Contoh pertanyaan yang bisa langsung diklik
    public const CONTOH = [
        'Ada telur dan santan, enaknya masak apa?',
        'Mau masak yang cepat, di bawah 30 menit',
        'Camilan manis buat sore hari',
        'Makanan berkuah hangat buat musim hujan',
    ];

    // mount() = dijalankan sekali saat komponen pertama kali dibuat (setiap halaman dibuka)
    public function mount(): void
    {
        $this->percakapan = session(self::KUNCI_SESSION, []);
    }

    public function tanya(): void
    {
        $this->pertanyaan = trim($this->pertanyaan);
        $this->validate(
            ['pertanyaan' => 'required|string|min:3|max:300'],
            ['pertanyaan.required' => 'Tulis dulu pertanyaanmu.', 'pertanyaan.min' => 'Pertanyaannya terlalu pendek.'],
        );

        if ($this->menunggu) {
            return; // masih menunggu jawaban sebelumnya
        }

        // Pembatas: setiap pengunjung (alamat IP) maks. 8 pertanyaan/menit dan 40/hari,
        // supaya kuota gratis Gemini tidak dihabiskan satu orang.
        $ip = request()->ip();
        foreach (["chef-ai:menit:{$ip}" => [8, 60], "chef-ai:hari:{$ip}" => [40, 86400]] as $kunci => [$maks, $detik]) {
            if (RateLimiter::tooManyAttempts($kunci, $maks)) {
                $this->addError('pertanyaan', 'Chef AI butuh istirahat sebentar 😅 Coba lagi dalam ' . ceil(RateLimiter::availableIn($kunci) / 60) . ' menit.');

                return;
            }
        }
        RateLimiter::hit("chef-ai:menit:{$ip}", 60);
        RateLimiter::hit("chef-ai:hari:{$ip}", 86400);

        $this->percakapan[] = ['peran' => 'user', 'teks' => $this->pertanyaan, 'resep_id' => []];
        $this->pertanyaan = '';
        $this->menunggu = true;

        // Minta browser langsung memanggil jawab() setelah tampilan diperbarui
        $this->js('$wire.jawab()');
    }

    public function tanyaContoh(int $nomor): void
    {
        $this->pertanyaan = self::CONTOH[$nomor] ?? '';
        $this->tanya();
    }

    public function jawab(OtakChefAi $chef): void
    {
        // Hanya jalan kalau memang ada pertanyaan yang belum dijawab
        $terakhir = end($this->percakapan);
        if (! $this->menunggu || ! $terakhir || $terakhir['peran'] !== 'user') {
            return;
        }

        $riwayat = array_slice($this->percakapan, 0, -1);

        try {
            $hasil = $chef->aktif()
                ? $chef->jawab($terakhir['teks'], $riwayat)
                : ['jawaban' => 'Chef AI belum diaktifkan di server ini (GEMINI_API_KEY belum diisi).', 'resep_id' => []];
        } catch (GeminiGagal $e) {
            Log::warning('Chef AI gagal: ' . $e->getMessage());
            $hasil = [
                'jawaban' => $e->kuotaHabis
                    ? 'Wah, Chef AI sedang kebanjiran pertanyaan 😅 Coba lagi beberapa saat lagi, ya.'
                    : 'Maaf, Chef AI sedang tidak bisa dihubungi. Coba lagi sebentar lagi, atau cari resep di halaman Jelajah.',
                'resep_id' => [],
            ];
        }

        $this->percakapan[] = ['peran' => 'chef', 'teks' => $hasil['jawaban'], 'resep_id' => $hasil['resep_id']];
        $this->percakapan = array_slice($this->percakapan, -self::MAKS_PESAN);
        $this->menunggu = false;

        // Simpan setelah pertanyaan DAN jawabannya lengkap
        session([self::KUNCI_SESSION => $this->percakapan]);
    }

    public function mulaiUlang(): void
    {
        $this->reset('percakapan', 'pertanyaan', 'menunggu');
        $this->resetErrorBag();
        session()->forget(self::KUNCI_SESSION);
    }

    public function render()
    {
        // Ambil data semua resep yang disebut di obrolan, sekali query saja.
        // Widget ini ada di setiap halaman, jadi kalau obrolan masih kosong, database tidak disentuh.
        $semuaId = collect($this->percakapan)->pluck('resep_id')->flatten()->unique();
        $resep = $semuaId->isEmpty()
            ? collect()
            : Resep::with('kategori')->whereIn('id', $semuaId)->get()->keyBy('id');

        return view('livewire.chef-ai', ['resep' => $resep, 'contoh' => self::CONTOH]);
    }
}
