<?php

namespace App\Models\Localizacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bairro extends Model
{
    use HasFactory;
    protected $fillable = ["nome_bairro", "comuna_id"];

    public function comuna() {
        return $this->belongsTo(Comuna::class, 'comuna_id');
    }

    public function comites() {
        return $this->hasMany(Comite::class, 'bairro_id');
    }
}
