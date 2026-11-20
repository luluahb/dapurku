<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep', function (Blueprint $table) {
            $table->id();

            // Penulis resep. Kalau user dihapus, resepnya ikut terhapus.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Kategori resep. Nama tabelnya ditulis, karena Laravel menebak "kategoris".
            // Tanpa cascade: kategori yang masih punya resep TIDAK BOLEH dihapus.
            $table->foreignId('kategori_id')->constrained('kategori');

            $table->string('judul');
            $table->string('slug')->unique();       // judul versi URL: rendang-daging-sapi
            $table->text('deskripsi');              // text = tulisan panjang
            $table->string('foto')->nullable();     // boleh kosong
            $table->unsignedSmallInteger('waktu');  // lama memasak, dalam menit
            $table->unsignedTinyInteger('porsi')->default(2);
            $table->string('kesulitan', 10)->default('Mudah'); // Mudah / Sedang / Sulit
            $table->unsignedInteger('dilihat')->default(0);    // berapa kali dibuka
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep');
    }
};
