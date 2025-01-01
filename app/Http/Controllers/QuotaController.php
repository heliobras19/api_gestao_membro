<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Bairro;
use App\Models\Localizacao\Comite;
use App\Models\Localizacao\Comuna;
use Illuminate\Http\Request;

class QuotaController extends Controller
{
    public function setoriais()
    {
        return Comite::where('tipo', 1)->get();
    }
}
