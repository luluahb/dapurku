<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('langkah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();
            $table->unsignedSmallInteger('urutan'); // langkah ke-1, ke-2, dst.
            $table->text('isi');                    // langkah bisa panjang, jadi pakai text
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('langkah');
    }
};
