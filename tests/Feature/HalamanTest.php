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
        $this->get('/resep/resep-yang-tidak-ada')->assertNotFound();
    }
}
