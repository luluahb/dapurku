<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('resep', function (Blueprint $table) {
            // Jumlah suka. Cukup angka, karena pengunjung tidak login
            // (resep mana yang disukai seseorang diingat oleh browser-nya sendiri).
            $table->unsignedInteger('suka')->default(0)->after('dilihat');
        });
    }

    public function down(): void
    {
        Schema::table('resep', function (Blueprint $table) {
            $table->dropColumn('suka');
        });
    }
};
