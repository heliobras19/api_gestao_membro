<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Liugua extends Model
{
    use HasFactory;
    protected $fillable = ['lingua', 'tipo', 'membro_id'];
}
