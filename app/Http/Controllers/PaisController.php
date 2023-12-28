<?php

namespace App\Http\Controllers;

use App\Models\Localizacao\Bairro;
use App\Models\Localizacao\Provincia;
use App\Services\APIResponse;
use Illuminate\Http\Request;

class PaisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function pais (Request $request) {
        if($request->has('provincia_id')){
            $provincia = Provincia::with('municipios.comunas.bairros.comites')->where('id', $request->provincia_id)->get();
            return $provincia;
        }
        return Provincia::with('municipios.comunas.bairros.comites')->get();
    }

    public function bairros (){
        return response()->json(
            APIResponse::response(Bairro::all())
        );
    }
}
