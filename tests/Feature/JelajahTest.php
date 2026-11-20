<?php

namespace Tests\Feature;

use App\Livewire\Jelajah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Test otomatis untuk halaman Jelajah.
// Jalankan dengan: php artisan test
// Database yang dipakai adalah SQLite sementara di memori (lihat phpunit.xml),
// jadi data asli di MySQL "dapurku" TIDAK tersentuh.
class JelajahTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true; // isi database sementara dengan seeder (14 resep contoh)

    public function test_halaman_jelajah_bisa_dibuka(): void
    {
        $this->get('/jelajah')->assertOk()->assertSee('14 resep');
    }

    public function test_mencari_lewat_nama_bahan(): void
    {
        // "santan" tidak ada di judul Rendang, tapi ada di bahannya
        Livewire::test(Jelajah::class)
            ->set('cari', 'santan')
            ->assertSee('Rendang Daging Sapi')
            ->assertDontSee('Pancake Pisang');
    }

    public function test_filter_kategori_bisa_dipilih_dan_dibatalkan(): void
    {
        Livewire::test(Jelajah::class)
            ->call('pilihKategori', 'sarapan')
            ->assertSet('kategori', 'sarapan')
            ->assertSee('Roti John')
            ->assertDontSee('Rendang Daging Sapi')
            ->call('pilihKategori', 'sarapan') // klik lagi = batal
            ->assertSet('kategori', '');
    }

    public function test_infinite_scroll_menambah_resep(): void
    {
        Livewire::test(Jelajah::class)
            ->assertViewHas('resep', fn ($resep) => $resep->count() === 6)
            ->call('muatLagi')
            ->assertViewHas('resep', fn ($resep) => $resep->count() === 12)
            ->call('muatLagi')
            ->assertViewHas('resep', fn ($resep) => $resep->count() === 14)
            ->assertViewHas('adaLagi', false);
    }

    public function test_ganti_filter_mengembalikan_jumlah_ke_awal(): void
    {
        Livewire::test(Jelajah::class)
            ->call('muatLagi')
            ->assertSet('jumlah', 12)
            ->set('urut', 'populer')
            ->assertSet('jumlah', 6);
    }

    public function test_tidak_ada_hasil(): void
    {
        Livewire::test(Jelajah::class)
            ->set('cari', 'xyzabc')
            ->assertSee('Resepnya belum ada');
    }
}
