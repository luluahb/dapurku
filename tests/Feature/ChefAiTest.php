<?php

namespace Tests\Feature;

use App\Livewire\ChefAi;
use App\Models\Resep;
use App\Services\ChefAi as OtakChefAi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;
use Livewire\Livewire;
use Tests\TestCase;

// Test Chef AI.
// Gemini TIDAK dihubungi sungguhan: semua permintaan HTTP dipalsukan dengan Http::fake().
// Jadi test gratis, cepat, tidak butuh internet, dan hasilnya selalu sama.
class ChefAiTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true; // 14 resep contoh

    private int $idRendang;

    protected function setUp(): void
    {
        parent::setUp();

        Http::preventStrayRequests(); // gagal kalau ada permintaan HTTP yang tidak dipalsukan
        Sleep::fake();                // jeda "coba ulang" tidak benar-benar ditunggu

        $this->idRendang = Resep::where('judul', 'like', 'Rendang%')->value('id');
    }

    /**
     * Gemini palsu.
     * - Embedding: vector sederhana [mengandung "santan"?, mengandung "manis"?, 0.1]
     * - Jawaban : merekomendasikan Rendang + id 9999 (id yang TIDAK ada, pura-pura halusinasi)
     */
    private function palsukanGemini(int $statusJawaban = 200, int $statusCadangan = 200): void
    {
        Http::fake(function (Request $request) use ($statusJawaban, $statusCadangan) {
            if (str_contains($request->url(), 'batchEmbedContents')) {
                return Http::response(['embeddings' => array_map(function ($r) {
                    $teks = mb_strtolower($r['content']['parts'][0]['text']);

                    return ['values' => [(float) str_contains($teks, 'santan'), (float) str_contains($teks, 'manis'), 0.1]];
                }, $request['requests'])]);
            }

            $cadangan = str_contains($request->url(), config('services.gemini.model_cadangan'));
            $status = $cadangan ? $statusCadangan : $statusJawaban;

            if ($status !== 200) {
                return Http::response(['error' => ['message' => 'high demand']], $status);
            }

            return Http::response(['candidates' => [['content' => ['parts' => [['text' => json_encode([
                'jawaban' => 'Coba Rendang, gurih dan pakai santan.' . ($cadangan ? ' (cadangan)' : ''),
                'resep_id' => [$this->idRendang, 9999],
            ])]]]]]]);
        });
    }

    private function tanya(string $pertanyaan)
    {
        return Livewire::test(ChefAi::class)
            ->set('pertanyaan', $pertanyaan)
            ->call('tanya')
            ->call('jawab'); // di browser, jawab() dipanggil otomatis setelah tanya()
    }

    public function test_halaman_chef_ai_bisa_dibuka_dan_ada_di_menu(): void
    {
        $this->get('/chef-ai')->assertOk()->assertSee('Mau masak apa hari ini?');
        $this->get('/')->assertSee(route('chef-ai'));
    }

    public function test_menjawab_dengan_resep_dari_database(): void
    {
        $this->palsukanGemini();

        $this->tanya('Ada santan, masak apa?')
            ->assertSet('menunggu', false)
            ->assertSee('Coba Rendang, gurih dan pakai santan.')
            ->assertSee('Rendang Daging Sapi')
            // id 9999 (halusinasi) dibuang, hanya id yang benar-benar ada yang dipakai
            ->assertSet('percakapan.1.resep_id', [$this->idRendang]);

        // RAG: resep hasil pencarian ikut dikirim ke Gemini sebagai bahan bacaan
        Http::assertSent(fn (Request $r) => str_contains($r->url(), 'generateContent')
            && str_contains(json_encode($r->data()), '<resep>')
            && str_contains(json_encode($r->data()), 'Rendang Daging Sapi'));
    }

    public function test_vector_resep_disimpan_di_cache(): void
    {
        $this->palsukanGemini();

        $this->tanya('santan');
        $this->tanya('yang manis');

        // Vector 14 resep hanya dibuat SEKALI; pertanyaan kedua memakai cache
        $jumlah = Http::recorded(fn (Request $r) => str_contains(json_encode($r->data()), 'RETRIEVAL_DOCUMENT'))->count();
        $this->assertSame(1, $jumlah);
    }

    public function test_pertanyaan_kosong_ditolak(): void
    {
        Livewire::test(ChefAi::class)
            ->set('pertanyaan', '   ')
            ->call('tanya')
            ->assertHasErrors('pertanyaan')
            ->assertSet('percakapan', []);

        Http::assertNothingSent();
    }

    public function test_maksimal_8_pertanyaan_per_menit(): void
    {
        $komponen = Livewire::test(ChefAi::class);

        for ($i = 0; $i < 8; $i++) {
            $komponen->set('pertanyaan', "pertanyaan {$i}")->call('tanya')->assertHasNoErrors()->set('menunggu', false);
        }

        $komponen->set('pertanyaan', 'pertanyaan ke-9')->call('tanya')
            ->assertHasErrors('pertanyaan')
            ->assertSee('Chef AI butuh istirahat sebentar');
    }

    public function test_pindah_ke_model_cadangan_kalau_model_utama_sibuk(): void
    {
        $this->palsukanGemini(statusJawaban: 503);

        $this->tanya('santan')->assertSee('Coba Rendang, gurih dan pakai santan. (cadangan)');
    }

    public function test_pesan_ramah_kalau_gemini_gagal(): void
    {
        $this->palsukanGemini(statusJawaban: 503, statusCadangan: 503);

        $this->tanya('santan')
            ->assertSee('Chef AI sedang tidak bisa dihubungi')
            ->assertSet('menunggu', false);
    }

    public function test_tanpa_api_key_chef_ai_tidak_aktif(): void
    {
        config(['services.gemini.key' => null]);

        $this->tanya('santan')->assertSee('Chef AI belum diaktifkan');
        Http::assertNothingSent();
    }

    public function test_rumus_kemiripan_cosine(): void
    {
        $this->assertEqualsWithDelta(1.0, OtakChefAi::kemiripan([1, 2], [2, 4]), 1e-9); // searah
        $this->assertEqualsWithDelta(0.0, OtakChefAi::kemiripan([1, 0], [0, 1]), 1e-9); // tegak lurus
    }
}
