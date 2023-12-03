<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Provincia;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function pais (Request $request) {
        if($request->has('provincia_id')){
            $provincia = Provincia::with('municipios.comunas.bairros.comites')->where('id', $request->provincia_id)->get();
            return $provincia;
        }
        return Provincia::with('municipios.comunas.bairros.comites')->get();
    }
}
