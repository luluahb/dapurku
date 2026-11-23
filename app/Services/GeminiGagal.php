<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use RuntimeException;

/**
 * Error khusus Gemini, supaya bisa ditangkap dan ditampilkan dengan pesan yang ramah.
 */
class GeminiGagal extends RuntimeException
{
    // 429 = terlalu banyak permintaan / kuota gratis habis
    public bool $kuotaHabis = false;

    // 5xx = server Google sedang bermasalah atau sibuk (contoh: 503 "high demand")
    public bool $sibuk = false;

    public static function dariRespons(Response $respons): self
    {
        $error = new self('Gemini menolak permintaan (HTTP ' . $respons->status() . '): '
            . $respons->json('error.message', 'tanpa pesan'));
        $error->kuotaHabis = $respons->status() === 429;
        $error->sibuk = $respons->serverError();

        return $error;
    }
}
