<?php

namespace App\Models\Localizacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provincia extends Model
{
    use HasFactory;
    protected $fillable = ["nome_provincia"];

    public function municipios (){
        return $this->hasMany(Municipio::class, 'provincia_id');
    }

    public function membros () {
        return $this->municipios()->with('comunas.bairros.comites.membros');
    }
}
