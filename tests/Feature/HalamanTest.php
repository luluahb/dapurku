<?php

namespace Tests\Feature;

use App\Models\Resep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test untuk Beranda dan halaman detail resep.
// Jalankan dengan: php artisan test
class HalamanTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_beranda_menampilkan_resep_dari_database(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('14 resep rumahan menunggumu')
            ->assertSee('Bubur Ayam Kampung'); // resep terbaru di seeder
    }

    public function test_detail_resep_bisa_dibuka_lewat_slug(): void
    {
        $this->get('/resep/bubur-ayam-kampung')
            ->assertOk()
            ->assertSee('Bubur Ayam Kampung')
            ->assertSee('200 gr beras'); // salah satu bahannya
    }

    public function test_dilihat_hanya_bertambah_sekali_per_sesi(): void
    {
        $awal = Resep::where('slug', 'bubur-ayam-kampung')->value('dilihat');

        $this->get('/resep/bubur-ayam-kampung');
        $this->get('/resep/bubur-ayam-kampung'); // dibuka lagi di sesi yang sama

        $this->assertSame($awal + 1, Resep::where('slug', 'bubur-ayam-kampung')->value('dilihat'));
    }

    public function test_slug_yang_salah_menampilkan_404(): void
    {
        $this->get('/resep/resep-yang-tidak-ada')
            ->assertNotFound()
            ->assertSee('resepnya hilang dari dapur'); // halaman 404 buatan sendiri
    }

    public function test_halaman_resep_punya_tag_pratinjau_link(): void
    {
        $resep = Resep::where('slug', 'bubur-ayam-kampung')->first();

        $this->get('/resep/bubur-ayam-kampung')
            ->assertSee('<meta property="og:title" content="Bubur Ayam Kampung · Dapurku">', false)
            ->assertSee('<meta property="og:image" content="' . $resep->foto_url . '">', false);
    }

    public function test_beranda_memakai_gambar_pratinjau_bawaan(): void
    {
        $this->get('/')->assertSee('images/og-dapurku.png', false);
    }
}
