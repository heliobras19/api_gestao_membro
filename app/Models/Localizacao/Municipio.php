<?php

namespace App\Models\Localizacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    protected $fillable = ['nome_municipio', 'provincia_id'];

    public function comunas () {
        return $this->hasMany(Comuna::class, 'municipio_id');
    }
    public function provincia () {
        return $this->belongsTo(Provincia::class, 'provincia_id');
    }
}
