<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcao extends Model
{
    protected $fillable = ['nome_funcao', 'tipo'];
    protected $appends = ['desc_tipo'];

    public function getDescTipoAttribute() {
        $tipo = [
               1 => "Deliberativo",
            "Executivo",
            "Aconselhamento",
            "Consultiva",
            "Jurisdicionais"
        ];
        return $tipo[$this->tipo];
    }
    use HasFactory;
}
