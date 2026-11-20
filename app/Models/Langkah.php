<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Langkah extends Model
{
    protected $table = 'langkah';

    protected $fillable = ['urutan', 'isi'];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}
