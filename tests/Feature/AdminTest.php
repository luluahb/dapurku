<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Resep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

// Test untuk login admin dan CRUD resep.
class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('is_admin', true)->first();
    }

    // Data form resep yang valid, bisa ditimpa sebagian lewat $ubah
    private function dataResep(array $ubah = []): array
    {
        return array_merge([
            'judul' => 'Soto Ayam Kuah Bening',
            'kategori_id' => Kategori::where('slug', 'sup-soto')->value('id'),
            'deskripsi' => 'Soto ayam dengan kuah bening yang segar dan harum rempah.',
            'waktu' => 50,
            'porsi' => 4,
            'kesulitan' => 'Sedang',
            'bahan' => ['1 ekor ayam', '', '2 batang serai'], // baris kosong harus diabaikan
            'langkah' => ['Rebus ayam.', 'Tumis bumbu dan masukkan ke kuah.'],
        ], $ubah);
    }

    // ===== Keamanan =====

    public function test_pengunjung_diarahkan_ke_login_saat_membuka_admin(): void
    {
        $this->get('/admin/resep')->assertRedirect(route('admin.login'));
    }

    public function test_user_biasa_tidak_bisa_login_walau_passwordnya_benar(): void
    {
        $this->post('/admin/login', ['email' => 'lia@contoh.test', 'password' => 'password123'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_user_biasa_yang_entah_bagaimana_login_tetap_ditolak(): void
    {
        $lia = User::where('email', 'lia@contoh.test')->first();
        $this->actingAs($lia)->get('/admin/resep')->assertForbidden(); // 403
    }

    public function test_admin_bisa_login_dan_logout(): void
    {
        $this->post('/admin/login', ['email' => 'admin@dapurku.test', 'password' => 'password123'])
            ->assertRedirect(route('admin.resep.index'));
        $this->assertAuthenticatedAs($this->admin());

        $this->post('/admin/logout')->assertRedirect(route('beranda'));
        $this->assertGuest();
    }

    // ===== CRUD =====

    public function test_admin_bisa_menambah_resep_dengan_foto(): void
    {
        Storage::fake('public'); // penyimpanan palsu, file asli tidak tersentuh

        $this->actingAs($this->admin())
            ->post('/admin/resep', $this->dataResep([
                'foto' => UploadedFile::fake()->image('soto.jpg', 800, 600),
            ]))
            ->assertRedirect(route('admin.resep.index'))
            ->assertSessionHas('pesan');

        $resep = Resep::where('judul', 'Soto Ayam Kuah Bening')->first();
        $this->assertSame('soto-ayam-kuah-bening', $resep->slug);
        $this->assertSame(['1 ekor ayam', '2 batang serai'], $resep->bahan->pluck('isi')->all());
        $this->assertCount(2, $resep->langkah);
        Storage::disk('public')->assertExists($resep->foto);

        // Resep baru langsung bisa dibuka pengunjung
        $this->get('/resep/soto-ayam-kuah-bening')->assertOk()->assertSee('2 batang serai');
    }

    public function test_validasi_menolak_data_kosong(): void
    {
        $this->actingAs($this->admin())
            ->post('/admin/resep', $this->dataResep(['judul' => '', 'bahan' => ['', ' '], 'kesulitan' => 'Gampang']))
            ->assertSessionHasErrors(['judul', 'bahan', 'kesulitan']);
    }

    public function test_admin_bisa_mengedit_resep(): void
    {
        $resep = Resep::where('slug', 'bubur-ayam-kampung')->first();

        $this->actingAs($this->admin())
            ->put("/admin/resep/{$resep->slug}", $this->dataResep([
                'judul' => 'Bubur Ayam Spesial',
                'bahan' => ['Beras', 'Ayam'],
            ]))
            ->assertRedirect(route('admin.resep.index'));

        $resep->refresh();
        $this->assertSame('Bubur Ayam Spesial', $resep->judul);
        $this->assertSame('bubur-ayam-kampung', $resep->slug); // slug sengaja tidak berubah
        $this->assertSame(['Beras', 'Ayam'], $resep->bahan->pluck('isi')->all());
        $this->assertStringStartsWith('http', $resep->foto); // foto lama dipertahankan
    }

    public function test_admin_bisa_menghapus_resep_beserta_bahannya(): void
    {
        $resep = Resep::where('slug', 'bubur-ayam-kampung')->first();

        $this->actingAs($this->admin())
            ->delete("/admin/resep/{$resep->slug}")
            ->assertRedirect(route('admin.resep.index'));

        $this->assertModelMissing($resep);
        $this->assertDatabaseMissing('bahan', ['resep_id' => $resep->id]);
    }

    public function test_halaman_admin_bisa_dibuka(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin/resep')->assertOk()->assertSee('Kelola resep');
        $this->get('/admin/resep/create')->assertOk()->assertSee('Tambah resep baru');
        $this->get('/admin/resep/bubur-ayam-kampung/edit')->assertOk()->assertSee('200 gr beras');
    }
}
