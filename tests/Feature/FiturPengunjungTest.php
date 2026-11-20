<?php

namespace Tests\Feature;

use App\Livewire\Favoritku;
use App\Livewire\KomentarResep;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

// Test untuk fitur pengunjung tanpa login: suka, favoritku, komentar & rating.
class FiturPengunjungTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function resep(): Resep
    {
        return Resep::where('slug', 'bubur-ayam-kampung')->first();
    }

    // ===== ❤️ SUKA =====

    public function test_suka_menambah_satu_dan_tidak_bisa_berulang(): void
    {
        $resep = $this->resep();
        $awal = $resep->suka;

        $this->postJson("/resep/{$resep->slug}/suka", ['aksi' => 'tambah'])
            ->assertOk()->assertJson(['suka' => $awal + 1]);

        // Klik "tambah" lagi di sesi yang sama → tidak bertambah
        $this->postJson("/resep/{$resep->slug}/suka", ['aksi' => 'tambah'])
            ->assertJson(['suka' => $awal + 1]);

        // Batal suka → kembali seperti awal
        $this->postJson("/resep/{$resep->slug}/suka", ['aksi' => 'kurang'])
            ->assertJson(['suka' => $awal]);
    }

    public function test_tidak_bisa_mengurangi_suka_yang_tidak_pernah_diberikan(): void
    {
        $resep = $this->resep();

        $this->postJson("/resep/{$resep->slug}/suka", ['aksi' => 'kurang'])
            ->assertJson(['suka' => $resep->suka]);
    }

    public function test_aksi_suka_yang_aneh_ditolak(): void
    {
        $this->postJson("/resep/{$this->resep()->slug}/suka", ['aksi' => 'hapus-semua'])
            ->assertUnprocessable(); // 422
    }

    // ===== 🔖 FAVORITKU =====

    public function test_favoritku_menampilkan_resep_sesuai_id_dari_browser(): void
    {
        $ids = Resep::whereIn('slug', ['bubur-ayam-kampung', 'roti-john-telur-daging'])->pluck('id')->all();

        Livewire::test(Favoritku::class)
            ->assertSet('sudahDimuat', false)
            ->call('muat', $ids)
            ->assertSee('Bubur Ayam Kampung')
            ->assertSee('Roti John Telur Daging')
            ->assertDontSee('Rendang Daging Sapi');
    }

    public function test_favoritku_kosong_dan_mengabaikan_data_aneh(): void
    {
        Livewire::test(Favoritku::class)
            ->call('muat', ['<script>', 'abc'])
            ->assertSet('ids', [])
            ->assertSee('Belum ada resep yang disimpan');
    }

    // ===== 💬 KOMENTAR & RATING =====

    public function test_pengunjung_bisa_mengirim_komentar(): void
    {
        $resep = $this->resep();
        $jumlahAwal = $resep->komentar()->count();

        Livewire::test(KomentarResep::class, ['resep' => $resep])
            ->set('nama', 'Wulan')
            ->set('rating', 5)
            ->set('isi', 'Enak sekali, keluarga suka!')
            ->call('kirim')
            ->assertHasNoErrors()
            ->assertDispatched('komentar-terkirim')
            ->assertSet('isi', '')          // isi dikosongkan
            ->assertSet('nama', 'Wulan')    // nama tetap diingat
            ->assertSee('Enak sekali, keluarga suka!');

        $this->assertSame($jumlahAwal + 1, $resep->komentar()->count());
    }

    public function test_komentar_tanpa_bintang_ditolak(): void
    {
        Livewire::test(KomentarResep::class, ['resep' => $this->resep()])
            ->set('nama', 'Wulan')
            ->set('isi', 'Enak sekali!')
            ->call('kirim')
            ->assertHasErrors(['rating']);
    }

    public function test_komentar_dibatasi_tiga_per_menit(): void
    {
        $uji = Livewire::test(KomentarResep::class, ['resep' => $this->resep()]);

        foreach (range(1, 3) as $n) {
            $uji->set('nama', 'Spammer')->set('rating', 1)->set('isi', "Komentar ke-{$n}")->call('kirim')->assertHasNoErrors();
        }

        $uji->set('rating', 1)->set('isi', 'Komentar ke-4')->call('kirim')->assertHasErrors(['isi']);
    }

    public function test_hanya_admin_yang_bisa_menghapus_komentar(): void
    {
        $resep = $this->resep();
        $komentar = $resep->komentar()->create(['nama' => 'X', 'rating' => 1, 'isi' => 'Komentar untuk dihapus']);

        // Pengunjung biasa → ditolak (403)
        Livewire::test(KomentarResep::class, ['resep' => $resep])
            ->call('hapus', $komentar->id)
            ->assertForbidden();

        // Admin → berhasil
        Livewire::actingAs(User::where('is_admin', true)->first())
            ->test(KomentarResep::class, ['resep' => $resep])
            ->call('hapus', $komentar->id);

        $this->assertModelMissing($komentar);
    }

    public function test_jelajah_bisa_diurutkan_berdasarkan_rating(): void
    {
        $this->get('/jelajah?urut=rating')->assertOk();
    }
}
