<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoQuota extends Model
{
    use HasFactory;
    protected $fillable = ['tipo_quota', 'montante', 'cod_quota'];
    public $appends = ['desc_quota'];
    public function getDescQuotaAttribute() {
        $tipos = [
            1 => 'Ordinário',
            "Extraordinario",
            "Doações"
        ];
        return $tipos[$this->cod_quota];
    }
}
