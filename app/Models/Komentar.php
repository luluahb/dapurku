<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Komentar extends Model
{
    protected $table = 'komentar';

    protected $fillable = ['nama', 'rating', 'isi'];

    public function resep()
    {
        return $this->belongsTo(Resep::class);
    }
}
