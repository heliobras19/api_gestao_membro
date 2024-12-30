<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BancosAtuorizados extends Model
{
    use HasFactory;
       protected $fillable = [
            'nome_banco', 
            'nome_conta', 
            'numero_conta', 
            'iban'
        ];
}
