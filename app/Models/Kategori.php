<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = ['nama', 'slug', 'ikon'];

    // Satu kategori punya banyak resep
    public function resep()
    {
        return $this->hasMany(Resep::class);
    }

    // Di URL, kategori dicari lewat slug-nya (/kategori/makanan-utama), bukan id-nya
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
