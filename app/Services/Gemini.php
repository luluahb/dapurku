<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * "Kurir" untuk berbicara dengan Google Gemini API.
 *
 * Class ini sengaja HANYA mengurus komunikasi (kirim permintaan, terima jawaban).
 * Logika Chef AI (mencari resep, menyusun prompt) ada di ChefAi.php.
 * Dengan dipisah, kalau suatu saat ganti penyedia AI, cukup class ini yang diubah.
 */
class Gemini
{
    private const URL = 'https://generativelanguage.googleapis.com/v1beta/models/';

    // Panjang vector. Model aslinya 3072 angka; 768 sudah cukup akurat dan 4x lebih hemat tempat.
    public const DIMENSI = 768;

    public function aktif(): bool
    {
        return filled(config('services.gemini.key'));
    }

    /**
     * Mengubah banyak teks sekaligus menjadi vector (embedding).
     *
     * $tugas memberi tahu model untuk apa vector ini:
     *  - RETRIEVAL_DOCUMENT : teks yang akan DICARI (isi resep)
     *  - RETRIEVAL_QUERY    : teks PENCARIANNYA (pertanyaan pengguna)
     *
     * @param  list<string>  $daftarTeks
     * @return list<list<float>> satu vector untuk setiap teks, urutannya sama
     */
    public function embed(array $daftarTeks, string $tugas): array
    {
        $model = config('services.gemini.model_embedding');
        $hasil = [];

        // API membatasi 100 teks per permintaan, jadi dikirim per kelompok 100
        foreach (array_chunk($daftarTeks, 100) as $kelompok) {
            $data = $this->kirim("{$model}:batchEmbedContents", [
                'requests' => array_map(fn (string $teks) => [
                    'model' => "models/{$model}",
                    'content' => ['parts' => [['text' => $teks]]],
                    'taskType' => $tugas,
                    'outputDimensionality' => self::DIMENSI,
                ], $kelompok),
            ]);

            foreach ($data['embeddings'] ?? [] as $embedding) {
                $hasil[] = $embedding['values'];
            }
        }

        if (count($hasil) !== count($daftarTeks)) {
            throw new GeminiGagal('Jumlah embedding dari Gemini tidak sesuai.');
        }

        return $hasil;
    }

    /**
     * Meminta jawaban dalam bentuk JSON yang strukturnya kita tentukan ($skema).
     * Jadi kita tidak perlu "menebak" isi jawaban teks bebas: hasilnya langsung array PHP.
     *
     * @param  list<array{role: string, parts: list<array{text: string}>}>  $percakapan
     */
    public function jawabJson(string $instruksi, array $percakapan, array $skema): array
    {
        $isi = [
            // systemInstruction = "aturan main" untuk AI, terpisah dari pesan pengguna
            'systemInstruction' => ['parts' => [['text' => $instruksi]]],
            'contents' => $percakapan,
            'generationConfig' => [
                'responseMimeType' => 'application/json',
                'responseSchema' => $skema,
                'temperature' => 0.4, // rendah = jawaban lebih konsisten, tidak "berimajinasi"
                'maxOutputTokens' => 800,
            ],
        ];

        // Coba model utama dulu. Kalau servernya sedang sibuk, pindah ke model cadangan.
        $daftarModel = array_values(array_unique(array_filter([
            config('services.gemini.model'),
            config('services.gemini.model_cadangan'),
        ])));

        foreach ($daftarModel as $urutan => $model) {
            try {
                $data = $this->kirim("{$model}:generateContent", $isi);
                break;
            } catch (GeminiGagal $e) {
                $masihAdaCadangan = $urutan < count($daftarModel) - 1;
                if (! $e->sibuk || ! $masihAdaCadangan) {
                    throw $e;
                }
            }
        }

        $teks = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
        $json = is_string($teks) ? json_decode($teks, true) : null;

        if (! is_array($json)) {
            throw new GeminiGagal('Jawaban Gemini bukan JSON yang valid.');
        }

        return $json;
    }

    private function kirim(string $tujuan, array $isi): array
    {
        try {
            $respons = Http::withHeaders(['x-goog-api-key' => config('services.gemini.key')])
                ->timeout(30)
                // Coba ulang maks. 2 kali (jeda 1 detik, lalu 2 detik) kalau koneksi putus
                // atau server Google sedang sibuk (HTTP 5xx). Error lain (misal key salah) tidak diulang.
                ->retry([1000, 2000], fn ($e) => $e instanceof ConnectionException
                    || ($e instanceof RequestException && $e->response->serverError()), throw: false)
                ->post(self::URL . $tujuan, $isi);
        } catch (ConnectionException $e) {
            throw new GeminiGagal('Tidak bisa terhubung ke Gemini.', previous: $e);
        }

        if ($respons->failed()) {
            throw GeminiGagal::dariRespons($respons);
        }

        return $respons->json();
    }
}
