<?php

namespace App\Models\Localizacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comuna extends Model
{
    use HasFactory;
    protected $fillable = ['nome_comuna', 'municipio_id'];

    public function municipio () {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }

    public function bairros () {
        return $this->hasMany(Bairro::class, 'comuna_id');
    }
}
