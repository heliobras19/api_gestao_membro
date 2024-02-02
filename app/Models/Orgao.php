<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orgao extends Model
{
    use HasFactory;
    protected $fillable = ['nome_orgao', 'tipo'];
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
}
