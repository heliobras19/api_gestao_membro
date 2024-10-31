<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;
     protected $fillable = [
        'tipo',
        'valor',
        'ano',
        'mes',
        'pagamento_id',
        'membro_id'
    ];

    public function pagamento () {
        return $this->belongsTo(Pagamento::class, 'pagamento_id');
    }
}
