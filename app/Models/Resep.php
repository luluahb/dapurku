<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Resep extends Model
{
    protected $table = 'resep';

    protected $fillable = [
        'user_id', 'kategori_id', 'judul', 'slug', 'deskripsi',
        'foto', 'waktu', 'porsi', 'kesulitan', 'dilihat',
    ];

    // ===== Otomatis membuat slug dari judul =====
    // booted() dijalankan Laravel sekali. "creating" = tepat SEBELUM resep baru disimpan.
    // Jadi kita tidak perlu menulis slug sendiri: "Rendang Daging" → "rendang-daging".
    protected static function booted(): void
    {
        static::creating(function (Resep $resep) {
            if (blank($resep->slug)) {
                $slug = Str::slug($resep->judul);

                // Kalau slug sudah dipakai resep lain, tambahkan huruf acak supaya tetap unik
                if (static::where('slug', $slug)->exists()) {
                    $slug .= '-' . Str::lower(Str::random(5));
                }

                $resep->slug = $slug;
            }
        });
    }

    // ===== Relasi =====

    // Resep ini ditulis oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Resep ini masuk ke satu kategori
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // Resep ini punya banyak bahan, diurutkan dari bahan ke-1
    public function bahan()
    {
        return $this->hasMany(Bahan::class)->orderBy('urutan');
    }

    // Resep ini punya banyak langkah, diurutkan dari langkah ke-1
    public function langkah()
    {
        return $this->hasMany(Langkah::class)->orderBy('urutan');
    }

    // ===== Accessor: kolom "buatan" =====
    // Dipakai dengan $resep->foto_url (padahal kolom foto_url tidak ada di tabel).
    // - Foto contoh di seeder berupa link lengkap (https://...) → dipakai langsung.
    // - Foto upload pengguna nanti berupa nama file di storage → diubah jadi link.
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->foto) {
                return null;
            }

            return Str::startsWith($this->foto, 'http')
                ? $this->foto
                : Storage::url($this->foto);
        });
    }

    // Di URL, resep dicari lewat slug-nya (/resep/rendang-daging), bukan id-nya
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
