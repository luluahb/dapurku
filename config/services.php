<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // Chef AI (Google Gemini). API key WAJIB di .env, jangan pernah ditulis di kode.
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
        // Model untuk menjawab: "flash-lite" = cepat & hemat, cukup untuk tanya-jawab resep
        'model' => env('GEMINI_MODEL', 'gemini-3.1-flash-lite'),
        // Model cadangan: dipakai kalau model utama sedang sibuk (HTTP 503 "high demand")
        'model_cadangan' => env('GEMINI_MODEL_CADANGAN', 'gemini-2.5-flash-lite'),
        // Model untuk embedding: mengubah teks menjadi deretan angka (vector)
        'model_embedding' => env('GEMINI_EMBEDDING_MODEL', 'gemini-embedding-001'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
