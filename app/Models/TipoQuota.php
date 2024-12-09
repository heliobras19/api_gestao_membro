<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoQuota extends Model
{
    use HasFactory;
    protected $fillable = ['tipo_quota', 'montante'];
}
