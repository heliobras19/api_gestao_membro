<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamento extends Model
{
    use HasFactory;
    protected $fillable = [
        'referencia_pagamento',
        'obs',
        'metodo_pagamento',
        'membro_id',
        'data_pagamento',
        'processado_por'
    ];

    public function quotas () {
        return $this->hasMany(Quota::class, 'pagamento_id');
    }
}
