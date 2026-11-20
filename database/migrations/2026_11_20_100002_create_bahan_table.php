<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahan', function (Blueprint $table) {
            $table->id();

            // Bahan ini milik resep yang mana. Resep dihapus → bahannya ikut terhapus.
            $table->foreignId('resep_id')->constrained('resep')->cascadeOnDelete();

            $table->unsignedSmallInteger('urutan'); // bahan ke-1, ke-2, dst.
            $table->string('isi');                  // contoh: 500 gr daging sapi
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahan');
    }
};
