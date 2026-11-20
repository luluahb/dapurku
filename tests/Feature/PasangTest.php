<?php

namespace Tests\Feature;

use App\Models\Resep;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// Test halaman pemasangan /_pasang/{kode}
class PasangTest extends TestCase
{
    use RefreshDatabase;

    public function test_tanpa_setup_token_halaman_tidak_ada(): void
    {
        config(['app.setup_token' => null]);
        $this->get('/_pasang/apa-saja')->assertNotFound();
    }

    public function test_kode_salah_ditolak(): void
    {
        config(['app.setup_token' => 'kode-benar-123']);
        $this->get('/_pasang/kode-salah')->assertNotFound();
    }

    public function test_kode_benar_menjalankan_migrate_dan_seed(): void
    {
        config(['app.setup_token' => 'kode-benar-123']);

        $this->get('/_pasang/kode-benar-123')
            ->assertOk()
            ->assertSee('SELESAI');

        $this->assertSame(14, Resep::count());

        // Dibuka lagi: aman, data tidak dobel
        $this->get('/_pasang/kode-benar-123')->assertOk();
        $this->assertSame(14, Resep::count());
    }
}
