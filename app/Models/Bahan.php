<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bahan extends Model
{
    protected $table = 'bahan';

    // resep_id tidak perlu di sini, karena diisi otomatis lewat $resep->bahan()->create(...)
    protected $fillable = ['urutan', 'isi'];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}
